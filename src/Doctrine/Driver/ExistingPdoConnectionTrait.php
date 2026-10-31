<?php

declare(strict_types=1);

namespace Zjk\SqlBlade\Doctrine\Driver;

use Doctrine\DBAL\Driver\Connection as DriverConnection;
use Doctrine\DBAL\Driver\PDO\Connection as PdoConnection;

/**
 * Lets a Doctrine DBAL driver reuse the \PDO instance that Laravel has already opened.
 */
trait ExistingPdoConnectionTrait
{
    public function __construct(private \PDO $pdo)
    {
    }

    /**
     * @param array<string, mixed> $params
     *
     * @psalm-suppress InternalMethod The constructor is the only way to hand DBAL an already opened \PDO.
     */
    public function connect(array $params): DriverConnection
    {
        return new PdoConnection($this->pdo);
    }
}
