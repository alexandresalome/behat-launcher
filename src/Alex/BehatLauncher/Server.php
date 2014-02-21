<?php

namespace Alex\BehatLauncher;

use Alex\BehatLauncher\Behat\MysqlStorage;
use Alex\BehatLauncher\Behat\ProjectList;
use Symfony\Component\Console\Output\OutputInterface;

class Server
{
    private $projectList;
    private $runStorage;
    private $runningUnits = array();
    private $output;

    public function __construct(ProjectList $projectList, MysqlStorage $runStorage)
    {
        $this->projectList = $projectList;
        $this->runStorage = $runStorage;
    }

    public function setOutput(OutputInterface $output = null)
    {
        $this->output = $output;
    }

    public function run()
    {
        $this->log('starting Behat Launcher server');
        $this->registerSignals();

        while (true) {
            $this->tick();
            $this->log('waiting 5 seconds');
            sleep(5);
        }
    }

    public function finish()
    {
        $this->verifyUnits(true);
    }

    public function tick()
    {
        $this->startUnit();
        $this->verifyUnits();
    }

    private function verifyUnits($force = false)
    {
        foreach ($this->runningUnits as $i => $units) {
            foreach ($units as $j => $unit) {
                if (!$force || !$unit->finish()) {
                    continue;
                }

                $this->log('finished unit#'.$unit->getId().' ('.$unit->getStatus().')');

                $this->runStorage->saveRunUnit($unit);
                unset($this->runningUnits[$i][$j]);
            }
        }
    }

    private function startUnit()
    {
        foreach ($this->projectList->getAll() as $project) {
            if (isset($this->runningUnits[$project->getName()]) && count($this->runningUnits[$project->getName()]) >= $project->getRunnerCount()) {
                continue;
            }

            $runnable = $this->runStorage->getRunnableUnit($project);
            if (!$runnable) {
                continue;
            }

            $this->log('starting unit #'.$runnable->getId());

            if (!isset($this->runningUnits[$project->getName()])) {
                $this->runningUnits[$project->getName()] = array();
            }

            $runnable->start($project);
            $this->runningUnits[$project->getName()][] = $runnable;
        }
    }

    private function log($text)
    {
        if (!$this->output) {
            return;
        }

        $this->output->writeln($text);
    }

    private function registerSignals()
    {
        if (function_exists('pcntl_signal')) {
            declare(ticks = 1);
            $sigHandler = function () {
                $this->finish();
                exit;
            };

            pcntl_signal(SIGQUIT, $sigHandler);
            pcntl_signal(SIGTERM, $sigHandler);
            pcntl_signal(SIGINT,  $sigHandler);
            pcntl_signal(SIGHUP,  $sigHandler);
            pcntl_signal(SIGUSR1, $sigHandler);
        }
    }
}
