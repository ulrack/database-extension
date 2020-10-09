<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\DatabaseExtension\Tests\Command\Db\Connection;

use Exception;
use RuntimeException;
use PHPUnit\Framework\TestCase;
use Ulrack\Dbal\Common\ConnectionInterface;
use Ulrack\Command\Common\Command\InputInterface;
use Ulrack\Command\Common\Command\OutputInterface;
use Ulrack\DatabaseExtension\Command\Db\Connection\TestCommand;
use Ulrack\DatabaseExtension\Factory\Extension\DatabaseConnectionsFactory;

/**
 * @coversDefaultClass \Ulrack\DatabaseExtension\Command\Db\Connection\TestCommand
 */
class TestCommandTest extends TestCase
{
    /**
     * @covers ::__invoke
     * @covers ::__construct
     *
     * @return void
     */
    public function testInvoke(): void
    {
        $databasesFactory = $this->createMock(DatabaseConnectionsFactory::class);
        $subject = new TestCommand($databasesFactory);
        $output = $this->createMock(OutputInterface::class);

        $databasesFactory->expects(static::once())
            ->method('getList')
            ->willReturn(['foo', 'bar']);

        $databasesFactory->expects(static::exactly(2))
            ->method('create')
            ->willReturn($this->createMock(ConnectionInterface::class));

        $subject->__invoke($this->createMock(InputInterface::class), $output);
    }

    /**
     * @covers ::__invoke
     * @covers ::__construct
     *
     * @return void
     */
    public function testInvokeFailing(): void
    {
        $databasesFactory = $this->createMock(DatabaseConnectionsFactory::class);
        $subject = new TestCommand($databasesFactory);
        $output = $this->createMock(OutputInterface::class);

        $databasesFactory->expects(static::once())
            ->method('getList')
            ->willReturn(['foo', 'bar']);

        $databasesFactory->expects(static::exactly(2))
            ->method('create')
            ->willThrowException(
                new Exception(
                    'No connection',
                    1,
                    new Exception()
                )
            );

        $this->expectException(RuntimeException::class);
        $subject->__invoke($this->createMock(InputInterface::class), $output);
    }
}
