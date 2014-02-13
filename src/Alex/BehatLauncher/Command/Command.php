<?php

namespace Alex\BehatLauncher\Command;

use Alex\BehatLauncher\Application;
use Symfony\Component\Console\Command\Command as BaseCommand;

class Command extends BaseCommand
{
    /**
     * @var Application
     */
    private $application;

    public function __construct(Application $application)
    {
        $this->application = $application;

        parent::__construct();
    }

    protected function getProjectList()
    {
        return $this->application['project_list'];
    }

    protected function getRunStorage()
    {
        return $this->application['run_storage'];
    }
}
