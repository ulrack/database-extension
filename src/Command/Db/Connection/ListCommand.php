<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\DatabaseExtension\Command\Db\Connection;

use Ulrack\Command\Common\Command\InputInterface;
use Ulrack\Command\Common\Command\OutputInterface;
use Ulrack\Command\Common\Command\CommandInterface;
use Ulrack\DatabaseExtension\Factory\Extension\DatabaseConnectionsFactory;

class ListCommand implements CommandInterface
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
        $output->outputText('Available connections:', true, 'title');
        $output->outputList($this->databasesFactory->getList());
    }
}
