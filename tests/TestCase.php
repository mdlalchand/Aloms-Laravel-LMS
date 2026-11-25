<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Guard rails against a very nasty accident.
     *
     * If config is cached (`php artisan config:cache`, which update.sh does on
     * production), Laravel ignores phpunit.xml's <env> values. The suite then
     * boots with the *real* credentials from .env and `RefreshDatabase` would
     * wipe the live database. So we force the test environment and a throw-away
     * in-memory SQLite database before any test trait can run a single query.
     */
    protected function refreshApplication(): void
    {
        parent::refreshApplication();

        $config = $this->app['config'];

        $config->set('app.env', 'testing');
        $config->set('database.default', 'sqlite');
        $config->set('database.connections.sqlite.database', ':memory:');
        $config->set('database.connections.sqlite.foreign_key_constraints', true);
        $config->set('cache.default', 'array');
        $config->set('session.driver', 'array');
        $config->set('queue.default', 'sync');
        $config->set('mail.default', 'array');
        $config->set('filesystems.default', 'local');
        $config->set('app.debug', true);

        $database = (string) $config->get('database.connections.'.$config->get('database.default').'.database');

        if ($database !== ':memory:' && ! str_ends_with($database, ':memory:')) {
            throw new RuntimeException(
                'Refusing to run tests against a persistent database ('.$database.'). '.
                'Run `php artisan config:clear` first, then `php artisan test` again.'
            );
        }
    }
}
