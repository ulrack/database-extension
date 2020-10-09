<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\DatabaseExtension\Tests\Factory\Extension;

use PHPUnit\Framework\TestCase;
use Ulrack\Dbal\Common\ConnectionInterface;
use Ulrack\Dbal\Common\ConnectionFactoryInterface;
use Ulrack\Services\Exception\DefinitionNotFoundException;
use Ulrack\DatabaseExtension\Factory\Extension\DatabaseConnectionsFactory;

/**
 * @coversDefaultClass \Ulrack\DatabaseExtension\Factory\Extension\DatabaseConnectionsFactory
 */
class DatabaseConnectionsFactoryTest extends TestCase
{
    /**
     * @covers ::registerService
     * @covers ::create
     * @covers ::resolveReference
     * @covers ::createDatabaseInstance
     *
     * @return void
     */
    public function testRegisterService(): void
    {
        $subject = $this->createPartialMock(
            DatabaseConnectionsFactory::class,
            [
                'preCreate',
                'getParameters',
                'getKey',
                'getServices',
                'getInternalService',
                'superCreate',
                'postCreate'
            ]
        );

        $configuredConnection = $this->createMock(ConnectionInterface::class);

        $subject->registerService('foo', $configuredConnection);

        $subject->method('getParameters')
            ->willReturn([]);

        $subject->expects(static::once())
            ->method('preCreate')
            ->willReturn(['serviceKey' => 'database-connections.foo']);

        $subject->method('getKey')
            ->willReturn('database-connections');

        $subject->expects(static::once())
            ->method('getServices')
            ->willReturn(
                [
                    'database-connections' => [
                        'foo' => ['my-connection-configuration'],
                        'bar' => ['my-connection']
                    ]
                ]
            );

        $subject->expects(static::once())
            ->method('postCreate')
            ->with(
                'database-connections.foo',
                $configuredConnection,
                []
            )->willReturn(
                ['return' => $configuredConnection]
            );

        $this->assertEquals(
            $configuredConnection,
            $subject->create('database-connections.foo')
        );
    }

    /**
     * @covers ::create
     *
     * @return void
     */
    public function testCreateNoService(): void
    {
        $subject = $this->createPartialMock(
            DatabaseConnectionsFactory::class,
            [
                'preCreate',
                'getParameters',
                'getKey',
                'getServices',
                'getInternalService',
                'superCreate',
                'postCreate'
            ]
        );

        $subject->expects(static::once())
            ->method('getParameters')
            ->willReturn([]);

        $subject->expects(static::once())
            ->method('preCreate')
            ->willReturn(['serviceKey' => 'database-connections.main']);

        $subject->method('getKey')
            ->willReturn('database-connections');

        $subject->expects(static::once())
            ->method('getServices')
            ->willReturn(
                [
                    'database-connections' => [
                        'bar' => ['my-connection']
                    ]
                ]
            );

        $this->expectException(DefinitionNotFoundException::class);
        $subject->create('database-connections.foo');
    }

    /**
     * @covers ::getList
     *
     * @return void
     */
    public function testGetList(): void
    {
        $subject = $this->createPartialMock(
            DatabaseConnectionsFactory::class,
            [
                'preCreate',
                'getParameters',
                'getKey',
                'getServices',
                'getInternalService',
                'superCreate',
                'postCreate'
            ]
        );

        $subject->method('getKey')
            ->willReturn('database-connections');

        $subject->expects(static::once())
            ->method('getServices')
            ->willReturn(
                [
                    'database-connections' => [
                        'bar' => ['my-connection']
                    ]
                ]
            );


        $this->assertEquals(['bar'], $subject->getList());
    }

    /**
     * @covers ::create
     * @covers ::createDatabaseInstance
     * @covers ::resolveReference
     *
     * @return void
     */
    public function testCreate(): void
    {
        $subject = $this->createPartialMock(
            DatabaseConnectionsFactory::class,
            [
                'preCreate',
                'getParameters',
                'getKey',
                'getServices',
                'getInternalService',
                'superCreate',
                'postCreate'
            ]
        );

        $connection = $this->createMock(ConnectionInterface::class);
        $connectionFactory = $this->createMock(ConnectionFactoryInterface::class);

        $subject->method('getParameters')
            ->willReturn([]);

        $subject->expects(static::once())
            ->method('preCreate')
            ->willReturn(['serviceKey' => 'database-connections.foo']);

        $subject->method('getKey')
            ->willReturn('database-connections');

        $subject->expects(static::once())
            ->method('getServices')
            ->willReturn(
                [
                    'database-connections' => [
                        'foo' => [
                            'type' => 'pdo',
                            'driver' => 'mysql',
                            'host' => 'localhost',
                            'database' => 'foo',
                            'username' => 'bar',
                            'password' => '@{parameters.database.password}'
                        ],
                        'bar' => ['my-connection']
                    ]
                ]
            );

        $subject->expects(static::exactly(2))
            ->method('superCreate')
            ->withConsecutive(
                ['parameters.database.password'],
                ['services.db.connection.factory.pdo']
            )->willReturnOnConsecutiveCalls(
                'baz',
                $connectionFactory
            );

        $connectionFactory->expects(static::once())
            ->method('create')
            ->with(
                'mysql:host=localhost;dbname=foo',
                'bar',
                'baz',
                null,
                null
            )->willReturn($connection);

        $subject->expects(static::once())
            ->method('postCreate')
            ->with(
                'database-connections.foo',
                $connection,
                []
            )->willReturn(
                ['return' => $connection]
            );

        $this->assertEquals(
            $connection,
            $subject->create('database-connections.foo')
        );
    }
}
