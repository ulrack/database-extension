<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\DatabaseExtension\Tests\Command\Db\Connection;

use PHPUnit\Framework\TestCase;
use Ulrack\Command\Common\Command\InputInterface;
use Ulrack\Command\Common\Command\OutputInterface;
use Ulrack\DatabaseExtension\Command\Db\Connection\ListCommand;
use Ulrack\DatabaseExtension\Factory\Extension\DatabaseConnectionsFactory;

/**
 * @coversDefaultClass \Ulrack\DatabaseExtension\Command\Db\Connection\ListCommand
 */
class ListCommandTest extends TestCase
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
        $subject = new ListCommand($databasesFactory);
        $output = $this->createMock(OutputInterface::class);

        $databasesFactory->expects(static::once())
            ->method('getList')
            ->willReturn(['foo', 'bar']);

        $output->expects(static::once())
            ->method('outputList')
            ->with(['foo', 'bar']);

        $subject->__invoke($this->createMock(InputInterface::class), $output);
    }
}
