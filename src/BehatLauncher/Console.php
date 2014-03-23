<?php

namespace BehatLauncher;

use BehatLauncher\Command\InitDbCommand;
use BehatLauncher\Command\PurgeCommand;
use BehatLauncher\Command\RunCommand;
use Symfony\Component\Console\Application as ConsoleApplication;

class Console extends ConsoleApplication
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $commands)
    {
        parent::__construct('Behat Launcher');
        $this->addCommands($commands);
    }
}
