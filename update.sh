#!/usr/bin/env bash
#
# Redwan's Method LMS - safe update script
#
# What it does, in order:
#   1. makes a timestamped backup of the database + .env  (storage/backups/)
#   2. puts the site into maintenance mode
#   3. installs composer dependencies (skippable)
#   4. runs ONLY new migrations (php artisan migrate --force) - never a fresh wipe
#   5. rebuilds config/route/view caches, fixes storage links & permissions
#   6. brings the site back up (even if a step failed)
#
# It NEVER runs migrate:fresh, db:wipe or db:seed, so existing
# users / courses / orders / payments are preserved.
#
# Usage:
#   ./update.sh                 # interactive-safe defaults
#   ./update.sh --skip-composer # code-only update (vendor/ already current)
#   ./update.sh --dry-run       # show what would happen, change nothing
#
set -uo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$APP_DIR" || exit 1

SKIP_COMPOSER=0
DRY_RUN=0
for arg in "$@"; do
  case "$arg" in
    --skip-composer) SKIP_COMPOSER=1 ;;
    --dry-run)       DRY_RUN=1 ;;
    -h|--help)       sed -n '2,22p' "$0"; exit 0 ;;
    *) echo "Unknown option: $arg" ; exit 1 ;;
  esac
done

PHP_BIN="${PHP_BIN:-php}"
STAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP_DIR="storage/backups"
FAILED=0

say()  { printf '\n\033[1;36m==> %s\033[0m\n' "$*"; }
ok()   { printf '    \033[0;32m✓\033[0m %s\n' "$*"; }
warn() { printf '    \033[0;33m!\033[0m %s\n' "$*"; }
die()  { printf '    \033[0;31m✗\033[0m %s\n' "$*"; exit 1; }

run() {
  if [ "$DRY_RUN" -eq 1 ]; then
    echo "    [dry-run] $*"
  else
    "$@"
  fi
}

[ -f artisan ] || die "artisan not found - run this script from the Laravel project root."
[ -f .env ]    || die ".env missing. Copy .env.example to .env, set the DB credentials and run 'php artisan key:generate' once."

# ---------------------------------------------------------------- 0. sanity
say "Checking environment"
"$PHP_BIN" -v >/dev/null 2>&1 || die "PHP not found. Set PHP_BIN=/path/to/php ./update.sh"
ok "PHP: $("$PHP_BIN" -r 'echo PHP_VERSION;')"

ENV_VAL() { grep -E "^$1=" .env | tail -1 | cut -d= -f2- | tr -d '"' | tr -d "'" | tr -d '\r'; }
DB_CONNECTION="$(ENV_VAL DB_CONNECTION)"; DB_CONNECTION="${DB_CONNECTION:-sqlite}"
DB_DATABASE="$(ENV_VAL DB_DATABASE)"

# ------------------------------------------------------- 1. backup database
say "Backing up database (.env is backed up too)"
mkdir -p "$BACKUP_DIR" storage/framework/cache/data storage/framework/sessions \
         storage/framework/views storage/logs storage/app/public bootstrap/cache

if [ "$DRY_RUN" -eq 0 ]; then
  cp -p .env "$BACKUP_DIR/env-$STAMP.bak"
  ok ".env -> $BACKUP_DIR/env-$STAMP.bak"
fi

case "$DB_CONNECTION" in
  sqlite)
    DB_FILE="${DB_DATABASE:-database/database.sqlite}"
    [ -f "$DB_FILE" ] || warn "no sqlite file at $DB_FILE (nothing to back up yet)"
    if [ -f "$DB_FILE" ] && [ "$DRY_RUN" -eq 0 ]; then
      cp -p "$DB_FILE" "$BACKUP_DIR/sqlite-$STAMP.sqlite"
      ok "database -> $BACKUP_DIR/sqlite-$STAMP.sqlite"
    fi
    ;;
  mysql)
    if command -v mysqldump >/dev/null 2>&1; then
      # shellcheck disable=SC2046
      if [ "$DRY_RUN" -eq 0 ]; then
        mysqldump --single-transaction --quick --no-tablespaces \
          -h"$(ENV_VAL DB_HOST)" -P"$(ENV_VAL DB_PORT)" \
          -u"$(ENV_VAL DB_USERNAME)" -p"$(ENV_VAL DB_PASSWORD)" \
          "${DB_DATABASE:-laravel}" > "$BACKUP_DIR/mysql-$STAMP.sql" 2>/dev/null \
          && ok "database -> $BACKUP_DIR/mysql-$STAMP.sql" \
          || warn "mysqldump failed (is it installed / are the credentials right?) - stopping safely"
        [ -s "$BACKUP_DIR/mysql-$STAMP.sql" ] || die "empty dump - aborting so nothing is touched."
      fi
    else
      warn "mysqldump not found. Export the database from phpMyAdmin/cPanel first, then re-run."
      die "aborted: no database backup could be made."
    fi
    ;;
  pgsql)
    command -v pg_dump >/dev/null 2>&1 || die "pg_dump not found - make a manual backup first."
    [ "$DRY_RUN" -eq 0 ] && PGPASSWORD="$(ENV_VAL DB_PASSWORD)" pg_dump \
      -h "$(ENV_VAL DB_HOST)" -p "$(ENV_VAL DB_PORT)" -U "$(ENV_VAL DB_USERNAME)" \
      -f "$BACKUP_DIR/pgsql-$STAMP.sql" "$(ENV_VAL DB_DATABASE)" \
      && ok "database -> $BACKUP_DIR/pgsql-$STAMP.sql"
    ;;
  *) warn "unknown DB_CONNECTION '$DB_CONNECTION' - back it up manually." ;;
esac

# keep the 10 newest backups, drop the rest
if [ "$DRY_RUN" -eq 0 ]; then
  ls -1t "$BACKUP_DIR" 2>/dev/null | tail -n +11 | while read -r old; do
    rm -rf "${BACKUP_DIR:?}/$old"
  done
fi

# ---------------------------------------------------- 2. maintenance mode
cleanup_and_up() {
  if [ "$DRY_RUN" -eq 0 ]; then
    "$PHP_BIN" artisan up >/dev/null 2>&1 || true
  fi
}
trap 'cleanup_and_up' EXIT INT TERM

say "Putting the site into maintenance mode"
if [ "$DRY_RUN" -eq 0 ]; then
  "$PHP_BIN" artisan down --retry=60 >/dev/null 2>&1 && ok "maintenance mode on (retry 60s)" || warn "could not enable maintenance mode (continuing)"
else
  echo "    [dry-run] php artisan down"
fi

# ------------------------------------------------------ 3. dependencies
if [ "$SKIP_COMPOSER" -eq 1 ]; then
  say "Skipping composer install (--skip-composer)"
elif command -v composer >/dev/null 2>&1; then
  say "Installing composer dependencies"
  if [ "$DRY_RUN" -eq 0 ]; then
    if composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist; then
      ok "composer install done"
    else
      FAILED=1; die "composer install failed - your previous code is still in place, fix the error and re-run."
    fi
  else
    echo "    [dry-run] composer install --no-dev --optimize-autoloader"
  fi
else
  warn "composer binary not found - assuming vendor/ is already current"
fi

# --------------------------------------------- 4. migrations (additive only)
say "Running new migrations (existing data is kept)"
if [ "$DRY_RUN" -eq 1 ]; then
  "$PHP_BIN" artisan migrate --pretend --force 2>/dev/null | sed 's/^/    [dry-run] /' || true
else
  if "$PHP_BIN" artisan migrate --force; then
    ok "migrations applied"
  else
    FAILED=1; die "migration failed. Database backup is in $BACKUP_DIR - restore it, check the error, then re-run."
  fi
fi

# --------------------------------------------------------- 5. refresh caches
say "Rebuilding caches, links and permissions"
run "$PHP_BIN" artisan optimize:clear >/dev/null 2>&1 || true
if [ "$DRY_RUN" -eq 0 ]; then
  "$PHP_BIN" artisan config:cache   >/dev/null 2>&1 && ok "config cached"   || warn "config:cache skipped"
  "$PHP_BIN" artisan route:cache    >/dev/null 2>&1 && ok "routes cached"   || { warn "route:cache unsupported - clearing it instead"; "$PHP_BIN" artisan route:clear >/dev/null 2>&1 || true; }
  "$PHP_BIN" artisan view:cache     >/dev/null 2>&1 && ok "views cached"    || warn "view:cache skipped"
  [ -L public/storage ] || { "$PHP_BIN" artisan storage:link >/dev/null 2>&1 && ok "public/storage linked" || warn "storage:link failed (uploads may 404)"; }
  chmod -R ug+rwX storage bootstrap/cache 2>/dev/null && ok "storage/ + bootstrap/cache/ writable" || warn "chmod failed (fix permissions manually)"
else
  echo "    [dry-run] optimize:clear / config:cache / route:cache / view:cache / storage:link / chmod"
fi

# ---------------------------------------------------------------- 6. finish
say "Update finished"
ok "database backup: $BACKUP_DIR/ ($STAMP)"
ok "code, schema and caches are now on the new version"
if [ "$DRY_RUN" -eq 1 ]; then
  warn "dry run - nothing was changed"
else
  echo "    Site is back online. Open it once in a browser to confirm, and check storage/logs/laravel.log."
fi
