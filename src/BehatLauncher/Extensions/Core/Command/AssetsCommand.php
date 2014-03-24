<?php

namespace BehatLauncher\Extensions\Core\Command;

use Assetic\AssetWriter;
use Assetic\Extension\Twig\TwigFormulaLoader;
use Assetic\Extension\Twig\TwigResource;
use Assetic\Factory\LazyAssetManager;
use BehatLauncher\Extension\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class AssetsCommand extends AbstractCommand
{
    public function configure()
    {
        $this
            ->setName('assets')
            ->setDescription('Generates assets for Behat-Launcher')
        ;
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $this['assetic.writer']->writeManagerAssets($this['assetic.lazy_asset_manager']);
    }
}
