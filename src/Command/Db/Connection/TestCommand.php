<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\DatabaseExtension\Command\Db\Connection;

use Throwable;
use RuntimeException;
use Ulrack\Command\Common\Command\InputInterface;
use Ulrack\Command\Common\Command\OutputInterface;
use Ulrack\Command\Common\Command\CommandInterface;
use Ulrack\DatabaseExtension\Factory\Extension\DatabaseConnectionsFactory;

class TestCommand implements CommandInterface
{
    /**
     * Contains the databases service factory.
     *
     * @var DatabaseConnectionsFactory
     */
    private $databasesFactory;

    /**
     * Constructor.
     *
     * @param DatabasesFactory $databasesFactory
     */
    public function __construct(DatabaseConnectionsFactory $databasesFactory)
    {
        $this->databasesFactory = $databasesFactory;
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
        $errors = 0;

        foreach ($this->databasesFactory->getList() as $key) {
            $output->writeLine(
                sprintf('Checking database %s.', $key),
                'text',
                true
            );

            try {
                $this->databasesFactory->create($key);
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
