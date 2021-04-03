<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\DatabaseExtension\Tests\Command\Db\Connection;

use PHPUnit\Framework\TestCase;
use GrizzIt\Command\Common\Command\InputInterface;
use GrizzIt\Command\Common\Command\OutputInterface;
use GrizzIt\Configuration\Common\RegistryInterface;
use Ulrack\DatabaseExtension\Command\Db\Connection\ListCommand;

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
        $configRegistry = $this->createMock(RegistryInterface::class);
        $subject = new ListCommand($configRegistry);
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

        $output->expects(static::once())
            ->method('outputList')
            ->with(['my.connection']);

        $subject->__invoke($this->createMock(InputInterface::class), $output);
    }
}
