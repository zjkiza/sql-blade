<?php

declare(strict_types=1);

namespace Zjk\SqlBlade\Contract;

use Doctrine\DBAL\Result;

interface SqlBladeInterface
{
    /**
     * @param array<string, mixed> $args
     * @param array<string, int>   $types
     */
    public function executeQuery(string $queryPath, array $args = [], array $types = []): Result;
}
