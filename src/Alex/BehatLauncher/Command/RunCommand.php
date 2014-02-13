<?php

namespace Alex\BehatLauncher\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
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

        while (true) {
            $unit = $storage->getRunnableUnit($project);

            if (!$unit) {
                $output->writeln('Found no run to process. Try again in 5 seconds...');
                sleep(5);

                continue;
            }

            try {
                $output->writeln(sprintf("Processing unit#%s", $unit->getId()));
                $process = $unit->getProcess($projectList->get($unit->getRun()->getProjectName()));
            } catch (\InvalidArgumentException $e) {
                $output->write(sprintf('<error>Project %s not found.</error>', $unit->getRun()->getProjectName()));
            }
            $process->run();
            $unit->finish($process);
            $storage->saveRunUnit($unit);
        }
    }
}
