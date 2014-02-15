<?php

namespace Alex\BehatLauncher\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    private $currentUnit;
    private $currentProcess;

    public function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Runs Behat tests')
            ->addArgument('project', InputArgument::OPTIONAL, 'Project to run')
            ->setHelp(<<<HELP
Runs a test registered in Behat-Launcher.

If you pass no argument, runs will be executed by planning order.

You can pass an argument <info>project</info> to execute specifically a run of a project:

    %command.full_name% project-name

HELP
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $storage     = $this->getRunStorage();
        $projectList = $this->getProjectList();

        if (null !== $input->getArgument('project')) {
            $project = $projectList->get($input->getArgument('project'));
        } else {
            $project = null;
        }

        if (function_exists('pcntl_signal')) {
            declare(ticks = 1);
            $sigHandler = function () use ($storage) {
                if ($this->currentProcess) {
                    $this->currentProcess->stop();
                }

                if ($this->currentUnit) {
                    $this->currentUnit->reset();
                    $storage->saveRunUnit($this->currentUnit);
                }

                exit;
            };

            pcntl_signal(SIGQUIT, $sigHandler);
            pcntl_signal(SIGTERM, $sigHandler);
            pcntl_signal(SIGINT,  $sigHandler);
            pcntl_signal(SIGHUP,  $sigHandler);
            pcntl_signal(SIGUSR1, $sigHandler);
        }

        while (true) {
            $this->currentUnit = $storage->getRunnableUnit($project);

            if (!$this->currentUnit) {
                $output->writeln('Found no run to process. Try again in 5 seconds...');
                sleep(5);

                continue;
            }

            try {
                $output->writeln(sprintf("Processing unit#%s", $this->currentUnit->getId()));
                $process = $this->currentUnit->getProcess($projectList->get($this->currentUnit->getRun()->getProjectName()));
            } catch (\InvalidArgumentException $e) {
                $output->write(sprintf('<error>Project %s not found.</error>', $this->currentUnit->getRun()->getProjectName()));
            }
            $this->currentProcess = $process;
            $this->currentProcess->run();
            $this->currentUnit->finish($this->currentProcess);
            $storage->saveRunUnit($this->currentUnit);
        }
    }
}
