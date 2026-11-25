<?php

/*
|--------------------------------------------------------------------------
| PHPUnit bootstrap
|--------------------------------------------------------------------------
|
| Why this file exists: if a production deployment has run
| `php artisan config:cache`, Laravel reads bootstrap/cache/config.php and
| ignores the <env> values in phpunit.xml. The suite would then boot with the
| *real* DB credentials from .env, and RefreshDatabase would wipe live data.
|
| Dropping the cached config/routes before the tests boot forces Laravel to
| re-read .env + phpunit.xml (in-memory SQLite). The caches are disposable and
| are rebuilt by update.sh, so nothing is lost by removing them here.
|
*/

foreach (array_merge(
    [__DIR__.'/../bootstrap/cache/config.php'],
    glob(__DIR__.'/../bootstrap/cache/routes-*.php') ?: []
) as $cacheFile) {
    if (is_file($cacheFile)) {
        @unlink($cacheFile);
    }
}

require __DIR__.'/../vendor/autoload.php';
