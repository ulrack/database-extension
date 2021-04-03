<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\DatabaseExtension\Factory\Extension;

use GrizzIt\Dbal\Common\ConnectionInterface;
use GrizzIt\Dbal\Common\ConnectionFactoryInterface;
use GrizzIt\Services\Common\Factory\ServiceFactoryExtensionInterface;

class DatabaseConnectionsFactory implements ServiceFactoryExtensionInterface
{
    /**
     * Contains the instantiated databases.
     *
     * @var array
     */
    private array $databases = [];

    /**
     * Converts a service key and definition to an instance.
     *
     * @param string $key
     * @param mixed $definition
     * @param callable $create
     *
     * @return ConnectionInterface
     */
    public function create(
        string $key,
        mixed $definition,
        callable $create
    ): mixed {
        if (!isset($this->databases[$key])) {
            /** @var ConnectionFactoryInterface $databaseFactory */
            $databaseFactory = $create(
                'services.db.connection.factory.' . $definition['type']
            );

            $this->databases[$key] = $databaseFactory->create(
                sprintf(
                    '%s:host=%s%s',
                    $definition['driver'],
                    $definition['host'],
                    !empty($definition['database']) ? sprintf(
                        ';dbname=%s',
                        $definition['database']
                    ) : ''
                ),
                $definition['username'],
                $definition['password'] ?? null,
                $definition['options'] ?? null,
                $definition['attributes'] ?? null
            );
        }

        return $this->databases[$key];
    }
}
