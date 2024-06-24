<?php

declare(strict_types=1);

namespace Zjk\SqlBlade\Tests\Functionality;

use Doctrine\DBAL\TransactionIsolationLevel;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Zjk\SqlBlade\Contract\SqlBladeInterface;
use Zjk\SqlBlade\Exception\RuntimeException;
use Zjk\SqlBlade\Service\SqlBlade;
use Zjk\SqlBlade\Tests\Resource\Logger\SqlLogger;
use Zjk\SqlBlade\Tests\TestCase;

final class SqlBladeTest extends TestCase
{
    private SqlBladeInterface $sqlBlade;
    private SqlLogger $sqlLogger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlLogger = new SqlLogger(App::make(LogManager::class)); // @phpstan-ignore-line

        /* @var SqlBladeInterface $sqlBlade */
        $this->sqlBlade = new SqlBlade(
            App::make('db.connection')->getDoctrineConnection(), // @phpstan-ignore-line
            App::make('view'), // @phpstan-ignore-line
            $this->sqlLogger,
            Config::get('app.debug') // @phpstan-ignore-line
        );
    }

    public function testSuccessExecuteTransaction(): void
    {
        $this->sqlLogger->clearLoggedQueries();

        $this->sqlBlade->transaction(function (SqlBladeInterface $sqlBlade): void {
            $sqlBlade->executeQuery('select_without_blade');
        }, TransactionIsolationLevel::READ_UNCOMMITTED);

        $loggedQueries = $this->sqlLogger->getLoggedQueries();

        $this->assertStringContainsString('START TRANSACTION', $loggedQueries[0]);
        $this->assertStringContainsString('SELECT email', $loggedQueries[1]);
        $this->assertStringContainsString('COMMIT', $loggedQueries[2]);
    }

    public function testExpectExceptionForTheWrongIsolationLevel(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Transaction isolation level it\'s out the allowed range [1-4].');

        $this->sqlBlade->transaction(function (SqlBladeInterface $sqlBlade): void {
            $sqlBlade->executeQuery('select_without_blade');
        }, 5); // @phpstan-ignore-line
    }
}
