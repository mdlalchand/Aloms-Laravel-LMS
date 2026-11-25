#!/usr/bin/env bash
#
# Redwan's Method LMS - database + .env backup only (no code changes).
# Use this before any manual change, or from a daily cron job.
#
#   ./backup.sh
#   0 3 * * * cd /path/to/lms && ./backup.sh >> storage/logs/backup.log 2>&1
#
# Backups land in storage/backups/ and the 30 newest are kept.
#
set -uo pipefail
APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$APP_DIR" || exit 1

PHP_BIN="${PHP_BIN:-php}"
STAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP_DIR="storage/backups"
mkdir -p "$BACKUP_DIR"

[ -f .env ] || { echo "no .env here - run from the Laravel project root"; exit 1; }
cp -p .env "$BACKUP_DIR/env-$STAMP.bak"
echo ".env    -> $BACKUP_DIR/env-$STAMP.bak"

ENV_VAL() { grep -E "^$1=" .env | tail -1 | cut -d= -f2- | tr -d '"' | tr -d "'" | tr -d '\r'; }
DB_CONNECTION="$(ENV_VAL DB_CONNECTION)"; DB_CONNECTION="${DB_CONNECTION:-sqlite}"

case "$DB_CONNECTION" in
  sqlite)
    DB_FILE="${DB_DATABASE:-$(ENV_VAL DB_DATABASE)}"; DB_FILE="${DB_FILE:-database/database.sqlite}"
    [ -f "$DB_FILE" ] && { cp -p "$DB_FILE" "$BACKUP_DIR/sqlite-$STAMP.sqlite"; echo "database -> $BACKUP_DIR/sqlite-$STAMP.sqlite"; } \
                      || echo "no sqlite database at $DB_FILE"
    ;;
  mysql)
    command -v mysqldump >/dev/null 2>&1 || { echo "mysqldump missing - export from phpMyAdmin"; exit 1; }
    mysqldump --single-transaction --quick --no-tablespaces \
      -h"$(ENV_VAL DB_HOST)" -P"$(ENV_VAL DB_PORT)" -u"$(ENV_VAL DB_USERNAME)" -p"$(ENV_VAL DB_PASSWORD)" \
      "${DB_DATABASE:-$(ENV_VAL DB_DATABASE)}" > "$BACKUP_DIR/mysql-$STAMP.sql" 2>/dev/null
    [ -s "$BACKUP_DIR/mysql-$STAMP.sql" ] || { echo "dump failed (empty file)"; rm -f "$BACKUP_DIR/mysql-$STAMP.sql"; exit 1; }
    echo "database -> $BACKUP_DIR/mysql-$STAMP.sql"
    ;;
  pgsql)
    command -v pg_dump >/dev/null 2>&1 || { echo "pg_dump missing"; exit 1; }
    PGPASSWORD="$(ENV_VAL DB_PASSWORD)" pg_dump -h "$(ENV_VAL DB_HOST)" -p "$(ENV_VAL DB_PORT)" \
      -U "$(ENV_VAL DB_USERNAME)" -f "$BACKUP_DIR/pgsql-$STAMP.sql" "$(ENV_VAL DB_DATABASE)"
    echo "database -> $BACKUP_DIR/pgsql-$STAMP.sql"
    ;;
  *) echo "unknown DB_CONNECTION: $DB_CONNECTION"; exit 1 ;;
esac

ls -1t "$BACKUP_DIR" 2>/dev/null | tail -n +31 | while read -r old; do rm -rf "${BACKUP_DIR:?}/$old"; done
echo "done ($(ls -1 "$BACKUP_DIR" | wc -l | tr -d ' ') files in $BACKUP_DIR)"
