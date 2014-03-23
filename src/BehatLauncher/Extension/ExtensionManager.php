<?php

namespace BehatLauncher\Extension;

use BehatLauncher\Application;
use BehatLauncher\Exception\ExtensionManagerFrozenException;
use BehatLauncher\Extension\ExtensionInterface;

class ExtensionManager
{
    private $extensions = array();

    /**
     * Adds an extension to the extension manager.
     *
     * @throws ExtensionManagerFrozenException
     */
    public function addExtension(ExtensionInterface $extension)
    {
        $this->extensions[] = $extension;

        return $this;
    }

    /**
     * @return array
     */
    public function getModelPaths()
    {
        $paths = array();
        foreach ($this->extensions as $extension) {
            $path = $extension->getModelPath();
            if (!$path) {
                continue;
            }

            $paths[] = $path;
        }

        return $paths;
    }

    /**
     * @return array
     */
    public function getControllers()
    {
        $controllers = array();
        foreach ($this->extensions as $extension) {
            $controllers = array_merge($controllers, $extension->getControllers());
        }

        return $controllers;
    }

    public function getCommands(Application $app)
    {
        $commands = array();
        foreach ($this->extensions as $extension) {
            $commands = array_merge($commands, $extension->getCommands($app));
        }

        return $commands;
    }

    public function getTwigTemplatesDirs()
    {
        $dirs = array();
        foreach ($this->extensions as $extension) {
            $dir = $extension->getTwigTemplatesDir();
            if (!$dir) {
                continue;
            }

            $dirs[] = $dir;
        }

        return $dirs;
    }

    public function getAngularTemplatesDirs()
    {
        $dirs = array();
        foreach ($this->extensions as $extension) {
            $dir = $extension->getAngularTemplatesDir();
            if (!$dir) {
                continue;
            }

            $dirs[] = $dir;
        }

        return $dirs;
    }

    public function getResourcesDirs()
    {
        $dirs = array();
        foreach ($this->extensions as $extension) {
            $dir = $extension->getResourcesDir();
            if (!$dir) {
                continue;
            }

            $dirs[] = $dir;
        }

        return $dirs;
    }
}
