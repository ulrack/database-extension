<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\DatabaseExtension\Command\Db\Connection;

use GrizzIt\Command\Common\Command\InputInterface;
use GrizzIt\Command\Common\Command\OutputInterface;
use GrizzIt\Configuration\Common\RegistryInterface;
use GrizzIt\Command\Common\Command\CommandInterface;

class ListCommand implements CommandInterface
{
    /**
     * Contains the databases service factory.
     *
     * @var RegistryInterface
     */
    private $configRegistry;

    /**
     * Constructor.
     *
     * @param RegistryInterface $serviceRegsitry
     */
    public function __construct(RegistryInterface $configRegistry)
    {
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
        $output->outputText('Available connections:', true, 'title');
        $output->outputList(
            array_keys(
                array_merge(
                    ...array_column(
                        $this->configRegistry->toArray()['services'],
                        'database-connections'
                    )
                )
            )
        );
    }
}
