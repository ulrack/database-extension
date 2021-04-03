<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\DatabaseExtension\Tests\Command\Db\Connection;

use Exception;
use RuntimeException;
use PHPUnit\Framework\TestCase;
use GrizzIt\Dbal\Common\ConnectionInterface;
use GrizzIt\Command\Common\Command\InputInterface;
use GrizzIt\Command\Common\Command\OutputInterface;
use GrizzIt\Configuration\Common\RegistryInterface;
use GrizzIt\Services\Common\Factory\ServiceFactoryInterface;
use Ulrack\DatabaseExtension\Command\Db\Connection\TestCommand;

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
        $configRegistry = $this->createMock(RegistryInterface::class);
        $serviceFactory = $this->createMock(ServiceFactoryInterface::class);
        $subject = new TestCommand($serviceFactory, $configRegistry);
        $output = $this->createMock(OutputInterface::class);

        $configRegistry->expects(static::once())
            ->method('toArray')
            ->willReturn(
                [
                    'services' => [
                        [
                            'database-connections' => [
                                'my.connection' => []
                            ]
                        ]
                    ]
                ]
            );

        $serviceFactory->expects(static::once())
            ->method('create')
            ->with('database-connections.my.connection')
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
        $configRegistry = $this->createMock(RegistryInterface::class);
        $serviceFactory = $this->createMock(ServiceFactoryInterface::class);
        $subject = new TestCommand($serviceFactory, $configRegistry);
        $output = $this->createMock(OutputInterface::class);

        $configRegistry->expects(static::once())
            ->method('toArray')
            ->willReturn(
                [
                    'services' => [
                        [
                            'database-connections' => [
                                'my.connection' => [],
                                'my.other.connection' => []
                            ]
                        ]
                    ]
                ]
            );

        $serviceFactory->expects(static::exactly(2))
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
