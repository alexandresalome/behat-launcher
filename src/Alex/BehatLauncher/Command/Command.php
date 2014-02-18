<?php

namespace Alex\BehatLauncher\Command;

use Alex\BehatLauncher\Application;
use Symfony\Component\Console\Command\Command as BaseCommand;

class Command extends BaseCommand
{
    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;

        parent::__construct();
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    protected function getProjectList()
    {
        return $this->app['project_list'];
    }

    protected function getRunStorage()
    {
        return $this->app['run_storage'];
    }

    public function getDB()
    {
        return $this->app['db'];
    }
}
