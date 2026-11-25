# INSTALL, UPDATE & BACKUP — Redwan's Method LMS

Everything in this file is written so that **your database data (users, courses,
orders, payments, enrolments, certificates) is never wiped**. There is exactly
one rule to remember:

> **Never run `php artisan migrate:fresh`, `db:wipe`, `migrate:refresh` or
> `db:seed` on a live site.** Those rebuild/reseed the database. `migrate` (plain)
> only applies *new* migration files and leaves existing rows untouched.

---

## 1. Fresh install

### A. VPS / local machine (Ubuntu, macOS, Windows + WSL)

```bash
# requirements: PHP 8.2+ (pdo_sqlite or pdo_mysql, mbstring, openssl, fileinfo),
#               composer, Node 20+ only if you want to rebuild the CSS/JS
unzip redwans-method-lms.zip && cd lms

composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate

# SQLite (zero config - good for demo)
touch database/database.sqlite

# ...or MySQL: edit .env
#   DB_CONNECTION=mysql
#   DB_HOST=127.0.0.1
#   DB_PORT=3306
#   DB_DATABASE=lms
#   DB_USERNAME=lms_user
#   DB_PASSWORD=secret

php artisan migrate --seed      # first install only - creates tables + demo data
php artisan storage:link        # thumbnails, PDFs, avatars
php artisan serve               # http://127.0.0.1:8000
```

Run it permanently with nginx + php-fpm, or Laravel Octane/Supervisor if you
prefer. Point the web root at `lms/public` (never at the project root).

### B. Shared hosting / cPanel (no SSH-first workflow)

1. Upload the zip and `vendor/` (or run `composer install` from cPanel *Terminal*).
2. Put the files **one level above** `public_html`, then copy the contents of
   `public/` into `public_html/` and edit `public_html/index.php`:
   ```php
   require __DIR__.'/../lms/vendor/autoload.php';
   $app = require_once __DIR__.'/../lms/bootstrap/app.php';
   ```
3. Create the MySQL database in cPanel, put the credentials in `.env`.
4. In Terminal: `php artisan key:generate && php artisan migrate --seed && php artisan storage:link`.
5. If `storage/` is not writable, set permission `755` (files `644`) on
   `storage/` and `bootstrap/cache/`.

> **Empty folders got dropped while unzipping?** Recreate them, otherwise Blade
> and sessions fail with HTTP 500:
> ```bash
> mkdir -p storage/framework/{cache/data,sessions,views} storage/logs storage/app/public bootstrap/cache
> ```

### After installing (production checklist)

- Log in as `admin@lms.test` / `password` and **change every demo password**
  (`/admin/students`), or delete the demo users.
- `APP_ENV=production`, `APP_DEBUG=false`, correct `APP_URL`.
- `php artisan config:cache && php artisan route:cache && php artisan view:cache`.
- Add the cron entry for scheduled tasks (if you enable any):
  `* * * * * cd /path/to/lms && php artisan schedule:run >> /dev/null 2>&1`
- Add a daily backup cron: `0 3 * * * cd /path/to/lms && ./backup.sh`.

---

## 2. Updating to a newer version (data-safe)

### The easy way — `update.sh`

```bash
cd /path/to/lms
./update.sh                # full update
./update.sh --dry-run      # preview every step, change nothing
./update.sh --skip-composer  # only if composer.lock did not change
```

`update.sh` does this, in this order:

| Step | Action |
|---|---|
| 1 | Backs up the **database** + `.env` to `storage/backups/<timestamp>-*` (keeps the 10 newest) |
| 2 | `php artisan down --retry=60` (maintenance mode, so nobody writes mid-update) |
| 3 | `composer install --no-dev --optimize-autoloader` |
| 4 | `php artisan migrate --force` — **only new migrations**, existing rows stay |
| 5 | `optimize:clear` → `config:cache`, `route:cache`, `view:cache`, `storage:link`, `chmod` |
| 6 | `php artisan up` — runs even if a step failed (trap on EXIT) |

If step 4 or 3 fails it **stops and leaves the site intact**, telling you where
the backup is. So the worst case is: restore the dump, fix the error, re-run.

### The manual way (same guarantee)

```bash
cd /path/to/lms

./backup.sh                     # 1. database + .env into storage/backups/
php artisan down --retry=60     # 2. maintenance mode

# 3. replace the CODE only - never .env, never storage/, never database/*.sqlite
#    git:  git pull --ff-only
#    zip:  copy app/ routes/ resources/views/ database/migrations/ public/build/
#          config/ tests/ composer.json composer.lock over the old files

composer install --no-dev --optimize-autoloader   # 4. dependencies
php artisan migrate --force                       # 5. only NEW migrations
php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan up                                    # 6. back online
```

### What is safe to overwrite vs. what must survive

| Overwrite freely (code) | **Never** overwrite / delete (data) |
|---|---|
| `app/`, `routes/`, `config/`, `resources/`, `database/migrations/`, `tests/`, `public/build/`, `composer.json/lock` | `.env`, `database/*.sqlite`, `storage/app/` (uploads), `storage/backups/`, `public/storage` (symlink) |

Course thumbnails, e-book PDFs, avatars and certificates live under
`storage/app/public` — they are **not** part of the code release, so replacing
files will not lose them. They are still worth including in your server-level
backups.

### Rolling back

```bash
php artisan down
php artisan migrate:rollback --step=1     # undo the last migration batch only
# git checkout <previous-tag>   (or unzip the previous release over the code)
composer install --no-dev --optimize-autoloader
php artisan optimize:clear && php artisan up
```

If a rollback is not enough, restore the dump made in step 1:

```bash
# SQLite
cp storage/backups/sqlite-<stamp>.sqlite database/database.sqlite
# MySQL
mysql -u user -p lms < storage/backups/mysql-<stamp>.sql
```

### Why updates are safe here

- Every migration is **additive**: `Schema::create(...)` or
  `Schema::table(... ->add column)` with a `down()` that only reverses its own
  change — no existing table is dropped or truncated.
- Seeders use `updateOrCreate` / `firstOrCreate`, so even an accidental re-seed
  updates demo rows instead of duplicating or clearing yours.
- New columns are added as nullable or with defaults, so old rows remain valid.
- `update.sh` refuses to continue when no database backup can be produced.

---

## 3. Backups

```bash
./backup.sh                    # database + .env -> storage/backups/
```

- Keeps the 30 newest files, deletes older ones.
- Works for SQLite (`cp`), MySQL (`mysqldump`) and PostgreSQL (`pg_dump`).
- Daily cron: `0 3 * * * cd /path/to/lms && ./backup.sh >> storage/logs/backup.log 2>&1`
- Off-server copy (recommended): rsync `storage/backups/` plus
  `storage/app/public/` to another machine/S3 once a day.

Restore test (do it once, before you need it): copy a dump into a scratch
database, point a copy of `.env` at it, run `php artisan migrate:status` and log
in. If that works, your backup is real.

---

## 4. Running the test suite without touching live data

`php artisan test` uses `RefreshDatabase`, which rebuilds the schema. That is
fine for a throw-away in-memory database, but disastrous if it ever points at
your real one. Two guards are built in:

1. **`tests/bootstrap.php`** deletes `bootstrap/cache/config.php` and
   `bootstrap/cache/routes-*.php` before the suite boots. Without this, a cached
   production config makes Laravel ignore `phpunit.xml`, and the suite would
   connect to the live database. (The caches are regenerated by `update.sh`.)
2. **`tests/TestCase.php`** forces `APP_ENV=testing`, `sqlite :memory:`, array
   cache/session and sync queue, then **throws a RuntimeException** if the
   database still resolves to a real file.

Verified on a database containing real rows, with config cached:

```
Tests:    16 passed (141 assertions)     # users=17, orders=14, marker row intact
```

Note: running the tests leaves `bootstrap/cache/config.php` deleted, so the site
runs uncached afterwards. Run `php artisan config:cache route:cache view:cache`
(or `./update.sh --skip-composer`) to re-enable the caches. Never run the test
suite on a production server "just to check" — use a staging copy.
