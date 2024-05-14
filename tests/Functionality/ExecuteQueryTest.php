<?php

declare(strict_types=1);

namespace Zjk\SqlBlade\Tests\Functionality;

use Doctrine\DBAL\ArrayParameterType;
use Illuminate\Support\Facades\App;
use Zjk\SqlBlade\Contract\SqlBladeInterface;
use Zjk\SqlBlade\Tests\TestCase;

final class ExecuteQueryTest extends TestCase
{
    private SqlBladeInterface $sqlBlade;

    protected function setUp(): void
    {
        parent::setUp();

        /* @var SqlBladeInterface $sqlBlade */
        $this->sqlBlade = App::get(SqlBladeInterface::class); // @phpstan-ignore-line
    }

    public function testWithOutBlade(): void
    {
        $response = $this->sqlBlade->executeQuery('select_without_blade')->fetchAllAssociative();

        $this->assertSame(
            [
                ['email' => 'bar@example.com'],
                ['email' => 'foo@example.com'],
            ],
            $response
        );
    }

    public function testFindByEmails(): void
    {
        $response = $this->sqlBlade->executeQuery('select_user', [
            'emails' => ['foo@example.com', 'bar@example.com'],
        ], [
            'emails' => ArrayParameterType::STRING,
        ])->fetchFirstColumn();

        $this->assertSame(
            [2, 1],
            $response
        );
    }

    public function testFindByIds(): void
    {
        $response = $this->sqlBlade->executeQuery('select_user', [
            'ids' => [2, 1],
        ], [
            'ids' => ArrayParameterType::INTEGER,
        ])->fetchFirstColumn();

        $this->assertSame(
            [1, 2],
            $response
        );
    }
}
