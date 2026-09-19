<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use RuntimeException;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $connection = $app['config']->get('database.default');
        $database = (string) $app['config']->get("database.connections.{$connection}.database");

        if ($database !== ':memory:' && ! str_contains($database, 'test')) {
            throw new RuntimeException(
                "Tests refuse to run against non-testing database '{$database}'. "
                .'Set DB_DATABASE to a *_testing database in phpunit.xml (with force="true").'
            );
        }

        return $app;
    }
}
