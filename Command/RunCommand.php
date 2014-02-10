<?php

namespace Alex\BehatLauncherBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this
            ->setName('behat-launcher:run')
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
        $storage = $this->getContainer()->get('behat_launcher.run_storage');

        while (true) {
            $run = $storage->getRunnable($input->getArgument('project'));

            if (!$run) {
                $output->writeln('Found no run to process. Try again in 5 seconds...');
                sleep(5);

                continue;
            }

        }
    }
}
