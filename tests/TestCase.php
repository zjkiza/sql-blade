<?php

declare(strict_types=1);

namespace Zjk\SqlBlade\Tests;

use Orchestra\Testbench\TestCase as Orchestra_TestCase;
use Zjk\SqlBlade\SqlBladeProvider;
use Zjk\SqlBlade\Tests\Resource\model\User;

class TestCase extends Orchestra_TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/Resource/migration');
        $this->artisan('migrate', ['--database' => 'testdb'])->run();

        $this->addUsers();
    }

    protected function getPackageProviders($app): array
    {
        return [
            SqlBladeProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testdb');
        $app['config']->set('database.connections.testdb', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        $app['config']->set('view.paths', ['tests/Resource/sql']);
    }

    protected function addUsers(): void
    {
        $user1 = new User();
        $user1->email = 'foo@example.com';
        $user1->save();

        $user1 = new User();
        $user1->email = 'bar@example.com';
        $user1->save();
    }
}
