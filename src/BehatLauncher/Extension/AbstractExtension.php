<?php

namespace BehatLauncher\Extension;

use BehatLauncher\Application;
use BehatLauncher\Extension\Utils\ClassDiscoverer;
use Symfony\Component\Finder\Finder;

abstract class AbstractExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $this->registerCommands($app);
        $this->registerControllers($app);
        $this->registerModel($app);
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectory()
    {
        $refl = new \ReflectionClass($this);

        return dirname($refl->getFilename());
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        $refl = new \ReflectionClass($this);
        $name = $refl->getName();

        return substr($name, 0, strrpos($name, '\\'));
    }


    private function registerCommands(Application $app)
    {
        if (!is_dir($this->getDirectory().'/Command')) {
            return;
        }

        $discoverer = new ClassDiscoverer();
        $classes    = $discoverer->discoverFinder(Finder::create()
            ->in($this->getDirectory().'/Command')
            ->name('*Command.php')
        );

        $app['console.commands'] = $app->share($app->extend('console.commands', function ($commands, $app) use ($classes) {
            return array_merge($commands, array_map(function ($class) use ($app) {
                return new $class($app);
            }, $classes));
        }));
    }

    private function registerControllers(Application $app)
    {
        if (!is_dir($this->getDirectory().'/Controller')) {
            return;
        }

        $discoverer = new ClassDiscoverer();
        $classes    = $discoverer->discoverFinder(Finder::create()
            ->in($this->getDirectory().'/Controller')
            ->name('*Controller.php')
        );

        foreach ($classes as $class) {
            $id = $class::id();
            $app[$id] = $app->share(function ($app) use ($class) {
                return new $class($app);
            });

            $class::route($app);
        }
    }

    private function registerModel(Application $app)
    {
        $dir = $this->getDirectory().'/Model';

        if (is_dir($dir)) {
            $app['em.model_dirs'] = $app->share($app->extend('em.model_dirs', function ($dirs, $app) use ($dir) {
                $dirs[] = $dir;

                return $dirs;
            }));
        }
    }
}
