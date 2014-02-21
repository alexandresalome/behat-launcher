<?php

namespace Alex\BehatLauncher\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    private $currentUnit;

    public function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Runs Behat tests')
            ->addOption('stop-on-finish', null, InputOption::VALUE_NONE, 'Don\'t wait for new runs to come')
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
        $server = $this->getServer();
        $server->setOutput($output);
        $server->run();
    }
}
