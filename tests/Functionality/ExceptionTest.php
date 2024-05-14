<?php

declare(strict_types=1);

namespace Zjk\SqlBlade\Tests\Functionality;

use Doctrine\DBAL\ArrayParameterType;
use Illuminate\Support\Facades\App;
use Zjk\SqlBlade\Tests\TestCase;
use Zjk\SqlBlade\Contract\SqlBladeInterface;
use Zjk\SqlBlade\Exception\LoaderErrorException;
use Zjk\SqlBlade\Exception\RuntimeException;
use Zjk\SqlBlade\Exception\SyntaxErrorException;

final class ExceptionTest extends TestCase
{
    private SqlBladeInterface $sqlBlade;

    protected function setUp(): void
    {
        parent::setUp();

        /* @var SqlBladeInterface $sqlBlade */
        $this->sqlBlade = App::get(SqlBladeInterface::class); // @phpstan-ignore-line
    }

    public function testExpectExceptionWhenNoPathOrFileExists(): void
    {
        $this->expectException(LoaderErrorException::class);
        $this->expectExceptionMessage('Could not find query source: lorem. View [lorem] not found.');

        $this->sqlBlade->executeQuery('lorem')->fetchAllAssociative();
    }

    public function testExpectExceptionWhenExistBladeSyntaxError(): void
    {
        $this->expectException(SyntaxErrorException::class);
        $this->expectExceptionMessage('Query source with_blade_error contains Blade syntax error. syntax error, unexpected token "endif", expecting end of file (View: /www/tests/Resource/sql/with_blade_error.blade.sql)');

        $this->sqlBlade->executeQuery('with_blade_error', [
            'ids' => [2, 1],
        ], [
            'ids' => ArrayParameterType::INTEGER,
        ])->fetchAllAssociative();
    }

    public function testExpectExceptionWhenVariableNotPassed(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The query "variable_not_passed" cannot be executed. An exception occurred while executing a query: SQLSTATE[HY000]: General error: 1 no such column: name');

        $this->sqlBlade->executeQuery('variable_not_passed')->fetchAllAssociative();
    }
}
