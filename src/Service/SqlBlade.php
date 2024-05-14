<?php

declare(strict_types=1);

namespace Zjk\SqlBlade\Service;

use Zjk\SqlBlade\Contract\SqlBladeInterface;
use Zjk\SqlBlade\Contract\SqlLoggerInterface;
use Zjk\SqlBlade\Exception\LoaderErrorException;
use Zjk\SqlBlade\Exception\RuntimeException;
use Zjk\SqlBlade\Exception\SyntaxErrorException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\ViewException;

final class SqlBlade implements SqlBladeInterface
{
    public function __construct(
        private Connection $connection,
        private Factory $factoryQuery,
        private SqlLoggerInterface $sqlLogger,
        private bool $isDebug
    ) {
    }

    /**
     * @throws LoaderErrorException
     * @throws SyntaxErrorException|ViewException|\JsonException
     */
    public function executeQuery(string $queryPath, array $args = [], array $types = []): Result
    {
        $sqlQuery = $this->getSql($queryPath, $args);
        $this->sqlLogger->startQuery($queryPath, $sqlQuery, $args, $types);

        try {
            return $this->connection->executeQuery($sqlQuery, $args, $types);
        } catch (\Exception $exception) {
            throw RuntimeException::formatted(\sprintf('The query "%s" cannot be executed. ', $queryPath), $exception->getMessage(), $this->isDebug);
        } finally {
            $this->sqlLogger->logQuery();
        }
    }

    /**
     * @param array<string, mixed> $args
     *
     * @throws LoaderErrorException
     * @throws SyntaxErrorException
     * @throws ViewException
     */
    private function getSql(string $queryPath, array $args): string
    {
        try {
            return $this->factoryQuery->make($queryPath, $args)->render();
        } catch (ViewException $exception) {
            throw SyntaxErrorException::formatted(\sprintf('Query source %s contains Blade syntax error. ', $queryPath), $exception->getMessage(), $this->isDebug);
        } catch (\InvalidArgumentException $exception) {
            throw LoaderErrorException::formatted(\sprintf('Could not find query source: %s. ', $queryPath), $exception->getMessage(), $this->isDebug);
        } catch (\Exception $exception) {
            throw RuntimeException::formatted(\sprintf('Query source %s contains unknown exception occurred. ', $queryPath), $exception->getMessage(), $this->isDebug);
        }
    }
}
