<?php

declare(strict_types=1);

namespace Zjk\SqlBlade\Contract;

use Doctrine\DBAL\Result;
use Doctrine\DBAL\TransactionIsolationLevel;

interface SqlBladeInterface
{
    /**
     * @param array<string, mixed> $args
     * @param array<string, int>   $types
     */
    public function executeQuery(string $queryPath, array $args = [], array $types = []): Result;

    /**
     * @param TransactionIsolationLevel::* $transactionIsolationLevel
     */
    public function transaction(\Closure $func, int $transactionIsolationLevel = TransactionIsolationLevel::READ_COMMITTED): ?Result;
}
