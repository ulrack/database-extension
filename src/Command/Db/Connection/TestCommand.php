<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\DatabaseExtension\Command\Db\Connection;

use Throwable;
use RuntimeException;
use GrizzIt\Command\Common\Command\InputInterface;
use GrizzIt\Command\Common\Command\OutputInterface;
use GrizzIt\Configuration\Common\RegistryInterface;
use GrizzIt\Command\Common\Command\CommandInterface;
use GrizzIt\Services\Common\Factory\ServiceFactoryInterface;

class TestCommand implements CommandInterface
{
    /**
     * Contains the databases service factory.
     *
     * @var ServiceFactoryInterface
     */
    private $serviceFactory;

    /**
     * Contains the databases service factory.
     *
     * @var RegistryInterface
     */
    private $configRegistry;

    /**
     * Constructor.
     *
     * @param ServiceFactoryInterface $serviceFactory
     * @param RegistryInterface $configRegistry
     */
    public function __construct(
        ServiceFactoryInterface $serviceFactory,
        RegistryInterface $configRegistry
    ) {
        $this->serviceFactory = $serviceFactory;
        $this->configRegistry = $configRegistry;
    }

    /**
     * Executes the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    public function __invoke(
        InputInterface $input,
        OutputInterface $output
    ): void {
        $output->writeLine('Fetching database configurations.', 'text', true);
        $connections = array_keys(
            array_merge(
                ...array_column(
                    $this->configRegistry->toArray()['services'],
                    'database-connections'
                )
            )
        );
        $errors = 0;

        foreach ($connections as $key) {
            $output->writeLine(
                sprintf('Checking database %s.', $key),
                'text',
                true
            );

            try {
                $this->serviceFactory->create('database-connections.' . $key);
                $output->outputBlock(
                    sprintf('Connection successful for database: %s', $key),
                    'success-block'
                );
            } catch (Throwable $exception) {
                $errors++;
                $output->outputBlock(
                    sprintf('Connection failed for database: %s', $key),
                    'error-block'
                );

                $output->writeLine('Reason:', 'text', true);
                $trace = $exception->getTraceAsString();
                $output->writeLine($exception->getMessage(), 'text', true);
                while ($exception = $exception->getPrevious()) {
                    $output->writeLine($exception->getMessage(), 'text', true);
                }

                $output->writeLine($trace, 'text', true);
            }
        }

        if ($errors > 0) {
            throw new RuntimeException(
                sprintf(
                    '%d database connection%s %s misconfigured.',
                    $errors,
                    $errors > 1 ? 's' : '',
                    $errors > 1 ? 'are' : 'is'
                )
            );
        }
    }
}
