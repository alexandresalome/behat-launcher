<?php

namespace Alex\BehatLauncher\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PurgeCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('purge')
            ->setDescription('Deletes data from Behat Launcher')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getRunStorage()->purge();

        $output->writeln('Database purged');
    }
}
