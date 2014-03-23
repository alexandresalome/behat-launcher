<?php

namespace Alex\BehatLauncher\Command;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitDbCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('init-db')
            ->setDescription('Creates Behat Launcher database')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $params = $this['connection']->getParams();
        $dbName = $params['dbname'];
        unset($params['dbname']);

        $conn = DriverManager::getConnection($params);

        $conn->exec('DROP DATABASE IF EXISTS '.$dbName);
        $conn->exec('CREATE DATABASE '.$dbName);

        $classes = $this['em']->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this['em']);

        foreach ($schemaTool->getCreateSchemaSql($classes) as $sql) {
            $this['connection']->exec($sql);
        }
    }
}
