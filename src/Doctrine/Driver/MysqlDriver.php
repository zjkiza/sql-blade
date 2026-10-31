<?php

declare(strict_types=1);

namespace Zjk\SqlBlade\Doctrine\Driver;

use Doctrine\DBAL\Driver\AbstractMySQLDriver;

/**
 * @psalm-suppress DeprecatedInterface DBAL's abstract driver still implements VersionAwarePlatformDriver.
 */
final class MysqlDriver extends AbstractMySQLDriver
{
    use ExistingPdoConnectionTrait;
}
