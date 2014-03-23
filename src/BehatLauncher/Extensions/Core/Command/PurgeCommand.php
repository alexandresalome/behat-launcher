<?php

namespace BehatLauncher\Extensions\Core\Command;

use BehatLauncher\Extension\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PurgeCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('purge')
            ->setDescription('Deletes data from Behat Launcher')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getRunStorage()->purge();

        $output->writeln('Database purged');
    }
}
