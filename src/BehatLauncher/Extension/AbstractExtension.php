<?php

namespace BehatLauncher\Extension;

use BehatLauncher\Application;
use Symfony\Component\Finder\Finder;

abstract class AbstractExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getModelPath()
    {
        $dir = $this->getDir();
        if (is_dir($dir.'/Model')) {
            return $dir;
        }

        return false;
    }

    public function getTwigTemplatesDir()
    {
        $dir = $this->getDir().'/Resources/views';

        return is_dir($dir) ? $dir : null;
    }

    public function getAngularTemplatesDir()
    {
        $dir = $this->getDir().'/Resources/templates';

        return is_dir($dir) ? $dir : null;
    }

    public function getResourcesDir()
    {
        $dir = $this->getDir().'/Resources';

        return is_dir($dir) ? $dir : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getControllers()
    {
        $dir = $this->getDir().'/Controller';
        if (!is_dir($dir)) {
            return array();
        }

        $finder = Finder::create()
            ->in($dir)
            ->name('*Controller.php')
        ;

        $refl = new \ReflectionClass($this);
        $name = $refl->getName();
        $prefix = substr($name, 0, strrpos($name, '\\')).'\\Controller\\';

        $controllers = array();
        foreach ($finder as $file) {
            $name = $file->getFilename();
            $controller = substr($name, 0, strrpos($name, '.'));
            $id = strtolower(substr($controller, 0, -10));
            $controllers[$id] = $prefix.$controller;
        }

        return $controllers;
    }

    public function getCommands(Application $app)
    {
        $dir = $this->getDir().'/Command';
        if (!is_dir($dir)) {
            return array();
        }

        $finder = Finder::create()
            ->in($dir)
            ->name('*Command.php')
        ;

        $refl = new \ReflectionClass($this);
        $name = $refl->getName();
        $prefix = substr($name, 0, strrpos($name, '\\')).'\\Command\\';

        $commands = array();
        foreach ($finder as $file) {
            $name = $file->getFilename();
            $controller = substr($name, 0, strrpos($name, '.'));
            $id = strtolower(substr($controller, 0, -7));
            $class = $prefix.$controller;
            $commands[] = new $class($app);
        }

        return $commands;
    }

    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
    }

    private function getDir()
    {
        $refl = new \ReflectionClass($this);
        return dirname($refl->getFilename());
    }
}
