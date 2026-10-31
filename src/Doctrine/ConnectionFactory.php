<?php

declare(strict_types=1);

namespace Zjk\SqlBlade\Doctrine;

use Doctrine\DBAL\Connection as DbalConnection;
use Doctrine\DBAL\Driver;
use Illuminate\Database\Connection as LaravelConnection;
use Zjk\SqlBlade\Doctrine\Driver\MysqlDriver;
use Zjk\SqlBlade\Doctrine\Driver\PostgresDriver;
use Zjk\SqlBlade\Doctrine\Driver\SqliteDriver;
use Zjk\SqlBlade\Doctrine\Driver\SqlServerDriver;
use Zjk\SqlBlade\Exception\RuntimeException;

/**
 * Laravel 11 removed `Connection::getDoctrineConnection()`, so the Doctrine DBAL connection
 * is built here on top of the \PDO instance owned by the Laravel connection.
 */
final class ConnectionFactory
{
    /**
     * @psalm-suppress InternalMethod The constructor is the only way to hand DBAL an already opened \PDO.
     */
    public function create(LaravelConnection $connection): DbalConnection
    {
        $pdo = $connection->getPdo();

        return new DbalConnection([], $this->driver($connection->getDriverName(), $pdo));
    }

    private function driver(string $driverName, \PDO $pdo): Driver
    {
        return match ($driverName) {
            'mysql', 'mariadb' => new MysqlDriver($pdo),
            'pgsql' => new PostgresDriver($pdo),
            'sqlite' => new SqliteDriver($pdo),
            'sqlsrv' => new SqlServerDriver($pdo),
            default => throw new RuntimeException(\sprintf('Database driver "%s" is not supported.', $driverName)),
        };
    }
}
