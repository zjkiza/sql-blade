<?php

declare(strict_types=1);

namespace Zjk\SqlBlade\Doctrine\Driver;

use Doctrine\DBAL\Driver\AbstractPostgreSQLDriver;

/**
 * @psalm-suppress DeprecatedInterface DBAL's abstract driver still implements VersionAwarePlatformDriver.
 */
final class PostgresDriver extends AbstractPostgreSQLDriver
{
    use ExistingPdoConnectionTrait;
}
