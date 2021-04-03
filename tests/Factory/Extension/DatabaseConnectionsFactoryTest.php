<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\DatabaseExtension\Tests\Factory\Extension;

use PHPUnit\Framework\TestCase;
use GrizzIt\Dbal\Common\ConnectionInterface;
use GrizzIt\Dbal\Common\ConnectionFactoryInterface;
use Ulrack\DatabaseExtension\Factory\Extension\DatabaseConnectionsFactory;

/**
 * @coversDefaultClass \Ulrack\DatabaseExtension\Factory\Extension\DatabaseConnectionsFactory
 */
class DatabaseConnectionsFactoryTest extends TestCase
{
    /**
     * @covers ::create
     *
     * @return void
     */
    public function testCreate(): void
    {
        $subject = new DatabaseConnectionsFactory();
        $connectionFactory = $this->createMock(ConnectionFactoryInterface::class);
        $connection = $this->createMock(ConnectionInterface::class);
        $create = function (string $key) use ($connectionFactory) {
            if ($key === 'services.db.connection.factory.pdo') {
                return $connectionFactory;
            }
        };

        $connectionFactory->expects(static::once())
            ->method('create')
            ->with(
                'mysql:host=localhost;dbname=mydb',
                'root'
            )->willReturn($connection);

        $this->assertEquals(
            $connection,
            $subject->create(
                'database-connections.foo',
                [
                    'type' => 'pdo',
                    'driver' => 'mysql',
                    'host' => 'localhost',
                    'database' => 'mydb',
                    'username' => 'root'
                ],
                $create
            )
        );

        $this->assertEquals(
            $connection,
            $subject->create('database-connections.foo', [], $create)
        );
    }
}
