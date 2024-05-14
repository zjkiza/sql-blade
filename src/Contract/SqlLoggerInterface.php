<?php

declare(strict_types=1);

namespace Zjk\SqlBlade\Contract;

interface SqlLoggerInterface
{
    /**
     * @param array<string, mixed> $args
     * @param array<string, int>   $types
     */
    public function startQuery(string $queryPath, string $sql, array $args = [], array $types = []): void;

    public function logQuery(): void;
}
