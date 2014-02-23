<?php

namespace Alex\BehatLauncher\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitDbCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('init-db')
            ->setDescription('Creates Behat Launcher database')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getRunStorage()->initDb();

        $output->writeln('Database initialized');
    }
}
