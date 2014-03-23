<?php

namespace BehatLauncher\Extensions\Core\Command;

use BehatLauncher\Extension\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Runs Behat-Launcher')
            ->addOption('stop-on-finish', null, InputOption::VALUE_NONE, 'Stop execution when all units are run')
            ->addOption('time-to-query', null, InputOption::VALUE_OPTIONAL, 'Time to wait for SQL queries', 2)
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $workspace = $this->getApplication()->getWorkspace();
        $workspace->setOutput($output);


        if (function_exists('pcntl_signal')) {
            declare(ticks = 1);
            $sigHandler = function () use ($workspace) {
                $workspace->finish();
                exit;
            };

            pcntl_signal(SIGQUIT, $sigHandler);
            pcntl_signal(SIGTERM, $sigHandler);
            pcntl_signal(SIGINT,  $sigHandler);
            pcntl_signal(SIGHUP,  $sigHandler);
            pcntl_signal(SIGUSR1, $sigHandler);
        }

        $stopOnFinish = $input->getOption('stop-on-finish');
        $timeToQuery = $input->getOption('time-to-query');
        $countRunning = 0;

        $currTime = $timeToQuery;
        $gcCycles = 100;
        do {
            $countRunning = $workspace->tick(true);
            $hasRunning = $countRunning;
            while (($hasRunning || !$stopOnFinish) && $currTime > 0) {
                usleep(100000);
                $currTime -= 0.1;
                $hasRunning = $workspace->tick();
            }
            $currTime = $timeToQuery;

            $gcCycles--;
            if ($gcCycles === 0) {
                gc_collect_cycles();
                $gcCycles = 100;
            }
        } while (!$stopOnFinish || $countRunning);
    }
}
