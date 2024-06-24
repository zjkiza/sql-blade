<?php

declare(strict_types=1);

namespace Zjk\SqlBlade\Service;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\TransactionIsolationLevel;
use Zjk\SqlBlade\Contract\SqlBladeInterface;
use Zjk\SqlBlade\Contract\SqlLoggerInterface;
use Zjk\SqlBlade\Exception\ExecuteException;
use Zjk\SqlBlade\Exception\LoaderErrorException;
use Zjk\SqlBlade\Exception\RuntimeException;
use Zjk\SqlBlade\Exception\SyntaxErrorException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\ViewException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
final class SqlBlade implements SqlBladeInterface
{
    private const TRANSACTION_ISOLATION_LEVEL = [
        TransactionIsolationLevel::READ_UNCOMMITTED,
        TransactionIsolationLevel::READ_COMMITTED,
        TransactionIsolationLevel::REPEATABLE_READ,
        TransactionIsolationLevel::SERIALIZABLE,
    ];

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

    public function transaction(\Closure $func, int $transactionIsolationLevel = TransactionIsolationLevel::READ_COMMITTED): ?Result
    {
        $this->validationTransactionIsolationLevel($transactionIsolationLevel);

        try {
            return $this->dbalTransaction($func, $transactionIsolationLevel);
        } catch (Exception $exception) {
            throw new ExecuteException('Unable to execute transaction', 0, $exception);
        }
    }

    /**
     * @param TransactionIsolationLevel::* $transactionIsolationLevel
     *
     * @throws Exception
     */
    private function dbalTransaction(\Closure $func, int $transactionIsolationLevel): ?Result
    {
        $previousIsolationLevel = $this->connection->getTransactionIsolation();

        $this->connection->setTransactionIsolation($transactionIsolationLevel);

        $this->sqlLogger->startQuery('TRANSACTION', '"START TRANSACTION"');
        $this->connection->beginTransaction();
        $this->sqlLogger->logQuery();

        try {
            $result = null;
            $result = $func($this);

            $this->sqlLogger->startQuery('TRANSACTION', '"COMMIT"');
            $this->connection->commit();
            $this->sqlLogger->logQuery();
        } catch (\Exception $exception) {
            $this->sqlLogger->startQuery('TRANSACTION', '"ROLLBACK"');
            $this->connection->rollBack();
            $this->sqlLogger->logQuery();

            throw $exception;
        } finally {
            $this->connection->setTransactionIsolation($previousIsolationLevel);
        }

        return $result;
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

    /**
     * @param TransactionIsolationLevel::* $transactionIsolationLevel
     */
    private function validationTransactionIsolationLevel(int $transactionIsolationLevel): void
    {
        /** @psalm-suppress DocblockTypeContradiction */
        if (!\in_array($transactionIsolationLevel, self::TRANSACTION_ISOLATION_LEVEL, true)) {
            throw new RuntimeException('Transaction isolation level it\'s out the allowed range [1-4].', 422);
        }
    }
}
