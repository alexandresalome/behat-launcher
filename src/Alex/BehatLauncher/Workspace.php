<?php

namespace Alex\BehatLauncher;

use Alex\BehatLauncher\Behat\MysqlStorage;
use Alex\BehatLauncher\Behat\ProjectList;
use Alex\BehatLauncher\Behat\RunUnit;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Workspace contains running units and processes.
 */
class Workspace
{
    private $projectList;
    private $runStorage;
    private $output;

    /**
     * @var array a collection of units, indexed by Project
     */
    private $runningUnits = array();

    /**
     * Instanciates workspace.
     */
    public function __construct(ProjectList $projectList, MysqlStorage $runStorage)
    {
        $this->projectList = $projectList;
        $this->runStorage = $runStorage;
    }

    /**
     * Sets the output for workspace to display informations about execution.
     *
     * @return Workspace
     */
    public function setOutput(OutputInterface $output = null)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Interrupt the workspace execution.
     *
     * @return Workspace
     */
    public function finish()
    {
        $this->verifyRunning(true);

        return $this;
    }

    /**
     * Verify pending units, and might run new units (if $startNew argument is true).
     *
     * @param boolean $startNew true to startNew for new runs
     *
     * @return int number of running units
     */
    public function tick($startNew = false)
    {
        $verify = $this->verifyRunning();

        if ($startNew) {
            $new  = $this->startUnits();
            if ($verify + $new == 0) {
                $this->output('no unit to run');
            }
        } else {
            $new = 0;
        }

        return $verify + $new;
    }

    /**
     * @param boolean $stop stops execution of running units, probably to stop execution
     *
     * @return int number of running units
     */
    private function verifyRunning($stop = false)
    {
        $count = 0;

        foreach ($this->runningUnits as $i => $units) {
            foreach ($units as $j => $unit) {
                if ($stop) {
                    $unit->reset();
                } elseif (!$unit->isProcessFinished()) {
                    $count++;

                    continue;
                }

                $this->output('finished unit#'.$unit->getId());

                $this->runStorage->saveRunUnit($unit);
                unset($this->runningUnits[$i][$j]);
            }
        }

        return $count;
    }

    /**
     * @return integer number of started units
     */
    private function startUnits()
    {
        $count = 0;

        $projectList = $this->projectList->getAll();

        $project = array_pop($projectList);
        while (true) {
            // verify a project is up
            if (null === $project) {
                break;
            }

            // verify project has not reached running limit
            if (isset($this->runningUnits[$project->getName()]) && count($this->runningUnits[$project->getName()]) >= $project->getRunnerCount()) {
                $project = array_pop($projectList);

                continue;
            }

            $runnable = $this->runStorage->getRunnableUnit($project);
            if (!$runnable) {
                $project = array_pop($projectList);

                continue;
            }

            $this->output('starting unit #'.$runnable->getId());

            if (!isset($this->runningUnits[$project->getName()])) {
                $this->runningUnits[$project->getName()] = array();
            }

            $runnable->start($project, function ($text, $error) use ($runnable) {
                $this->output($text, $runnable, $error);
            });
            $this->runningUnits[$project->getName()][] = $runnable;

            $count++;
        }

        return $count;
    }

    private function output($text, RunUnit $unit = null, $error = false)
    {
        if (!$this->output) {
            return;
        }

        if (false !== strpos($text, "\n")) {
            $exp = explode("\n", $text);
            foreach ($exp as $part) {
                $this->output($part, $unit, $error);
            }

            return;
        }

        if ($unit && $error) {
            $prefix = '<fg=red>#'.$unit->getId().'</fg=red> >';
        } elseif ($unit) {
            $prefix = '<fg=green>#'.$unit->getId().'</fg=green> >';
        } elseif ($error) {
            $prefix = '<fg=red>|</fg=red>';
        } else {
            $prefix = '<fg=green>|</fg=green>';
        }

        $this->output->writeln(sprintf(
            '%s %sMB %s %s',
            date('H:i:s'),
            round(memory_get_usage()/1024/1024),
            $prefix,
            $text
        ));
    }
}
