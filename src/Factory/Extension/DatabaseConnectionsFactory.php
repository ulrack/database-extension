<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\DatabaseExtension\Factory\Extension;

use Ulrack\Dbal\Common\ConnectionInterface;
use Ulrack\Dbal\Common\ConnectionFactoryInterface;
use Ulrack\Services\Exception\DefinitionNotFoundException;
use Ulrack\Services\Common\AbstractServiceFactoryExtension;

class DatabaseConnectionsFactory extends AbstractServiceFactoryExtension
{
    /**
     * Contains the instantiated databases.
     *
     * @var array
     */
    private $databases = [];

    /**
     * Register a value to a service key.
     *
     * @param string $serviceKey
     * @param mixed $value
     *
     * @return void
     */
    public function registerService(string $serviceKey, $value): void
    {
        $this->databases[$serviceKey] = $value;
    }

    /**
     * Retrieves a list of all possible services.
     *
     * @return array
     */
    public function getList(): array
    {
        return array_keys($this->getServices()[$this->getKey()]);
    }

    /**
     * Creates an instance of a database.
     *
     * @param string $key
     * @param array $database
     *
     * @return ConnectionInterface
     */
    private function createDatabaseInstance(
        string $key,
        array $database
    ): ConnectionInterface {
        if (!isset($this->databases[$key])) {
            /** @var ConnectionFactoryInterface $databaseFactory */
            $databaseFactory = $this->superCreate(
                'services.db.connection.factory.' . $database['type']
            );

            $this->databases[$key] = $databaseFactory->create(
                sprintf(
                    '%s:host=%s%s',
                    $database['driver'],
                    $database['host'],
                    !empty($database['database']) ? sprintf(
                        ';dbname=%s',
                        $database['database']
                    ) : ''
                ),
                $database['username'],
                $database['password'] ?? null,
                $database['options'] ?? null,
                $database['attributes'] ?? null
            );
        }

        return $this->databases[$key];
    }

    /**
     * Invoke the invocation and return the result.
     *
     * @param string $serviceKey
     *
     * @return mixed
     *
     * @throws DefinitionNotFoundException When the definition can not be found.
     */
    public function create(string $serviceKey)
    {
        $serviceKey = $this->preCreate(
            $serviceKey,
            $this->getParameters()
        )['serviceKey'];

        $internalKey = preg_replace(
            sprintf('/^%s\\./', preg_quote($this->getKey())),
            '',
            $serviceKey,
            1
        );

        $services = $this->getServices()[$this->getKey()];
        if (!isset($services[$internalKey])) {
            throw new DefinitionNotFoundException($serviceKey);
        }

        return $this->postCreate(
            $serviceKey,
            $this->createDatabaseInstance(
                $internalKey,
                $this->resolveReference(
                    $services[$internalKey]
                )
            ),
            $this->getParameters()
        )['return'];
    }

    /**
     * Resolves a reference to another service if applicable.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    private function resolveReference($value)
    {
        if (is_string($value) && $this->isReference($value)) {
            $value = $this->superCreate(trim($value, '@{}'));
        }

        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->resolveReference($item);
            }
        }

        return $value;
    }
}
