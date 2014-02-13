<?php

namespace Alex\BehatLauncher;

use Alex\BehatLauncher\Command\InitDbCommand;
use Alex\BehatLauncher\Command\PurgeCommand;
use Alex\BehatLauncher\Command\RunCommand;
use Symfony\Component\Console\Application as ConsoleApplication;

class Console extends ConsoleApplication
{
    public function __construct(Application $application)
    {
        parent::__construct('Behat Launcher', Application::VERSION);

        $this->add(new RunCommand($application));
        $this->add(new InitDbCommand($application));
        $this->add(new PurgeCommand($application));
    }
}
