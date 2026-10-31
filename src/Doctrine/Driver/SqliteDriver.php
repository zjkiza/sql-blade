<?php

declare(strict_types=1);

namespace Zjk\SqlBlade\Doctrine\Driver;

use Doctrine\DBAL\Driver\AbstractSQLiteDriver;

final class SqliteDriver extends AbstractSQLiteDriver
{
    use ExistingPdoConnectionTrait;
}
