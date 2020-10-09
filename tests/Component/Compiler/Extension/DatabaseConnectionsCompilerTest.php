<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\DatabaseExtension\Tests\Component\Compiler\Extension;

use PHPUnit\Framework\TestCase;
use Ulrack\Services\Common\ServiceRegistryInterface;
use GrizzIt\Validator\Component\Logical\AlwaysValidator;
use Ulrack\DatabaseExtension\Component\Compiler\Extension\DatabaseConnectionsCompiler;

/**
 * @coversDefaultClass \Ulrack\DatabaseExtension\Component\Compiler\Extension\DatabaseConnectionsCompiler
 */
class DatabaseConnectionsCompilerTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::compile
     */
    public function testCompiler(): void
    {
        $services = [
            'database-connections' => [
                'foo' => [
                    'type' => 'pdo',
                    'driver' => 'mysql',
                    'database' => '',
                    'host' => 'localhost',
                    'username' => 'user',
                    'password' => 'my-password'
                ]
            ]
        ];

        $subject = new DatabaseConnectionsCompiler(
            $this->createMock(ServiceRegistryInterface::class),
            'database-connections',
            new AlwaysValidator(true),
            [],
            [$this, 'getHooks']
        );

        $this->assertEquals($services, $subject->compile($services));
    }

    /**
     * Required method.
     *
     * @return array
     */
    public function getHooks(): array
    {
        return [];
    }
}
