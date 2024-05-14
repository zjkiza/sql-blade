<?php

declare(strict_types=1);

namespace Zjk\SqlBlade\Logger;

use Zjk\SqlBlade\Contract\SqlLoggerInterface;
use Illuminate\Log\LogManager;

class SqlLogger implements SqlLoggerInterface
{
    private ?float $start = null;

    /**
     * @var array{
     *     'sql' : string,
     *     'args' : array<string, mixed>,
     *     'types' : array<string, int>,
     *     'executionMS': float
     * }
     */
    private array $query;

    public function __construct(private LogManager $logManager)
    {
    }

    public function startQuery(string $queryPath, string $sql, array $args = [], array $types = []): void
    {
        $this->start = \microtime(true);

        $this->query = [
            'queryPath' => $queryPath,
            'sql' => $sql,
            'args' => $args,
            'types' => $types,
            'executionMS' => 0,
        ];
    }

    /**
     * @throws \JsonException
     */
    public function logQuery(): void
    {
        $this->query['executionMS'] = \microtime(true) - $this->start;
        $this->logManager->info(\sprintf('Executed SQL query: %s ', \json_encode($this->query, JSON_THROW_ON_ERROR)));
    }
}
