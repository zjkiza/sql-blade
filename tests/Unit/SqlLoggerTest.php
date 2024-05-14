<?php

declare(strict_types=1);

namespace Zjk\SqlBlade\Tests\Unit;

use Doctrine\DBAL\Connection;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use PHPUnit\Framework\TestCase;
use Zjk\SqlBlade\Logger\SqlLogger;
use Zjk\SqlBlade\Service\SqlBlade;

final class SqlLoggerTest extends TestCase
{
    public function testCalled(): void
    {
        $connection = $this->createMock(Connection::class);
        $factory = $this->createMock(Factory::class);
        $sqlLogger = $this->createMock(SqlLogger::class);

        $sqlBlade = new SqlBlade($connection, $factory, $sqlLogger, false);

        $queryPath = 'myQuery';
        $query = 'SELECT * FROM users';

        $view = $this->createMock(View::class);
        $view
            ->expects($this->once())
            ->method('render')
            ->willReturn($query);

        $factory
            ->expects($this->once())
            ->method('make')
            ->willReturn($view);

        $sqlLogger->expects($this->once())
            ->method('startQuery')
            ->with($queryPath, $query, [], []);

        $sqlLogger->expects($this->once())
            ->method('logQuery');

        $sqlBlade->executeQuery($queryPath)->fetchAllAssociative();
    }
}
