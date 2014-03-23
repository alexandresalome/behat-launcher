<?php

namespace Alex\BehatLauncher\Extension;

use Alex\BehatLauncher\Exception\ExtensionManagerFrozenException;
use Alex\BehatLauncher\Extension\ExtensionInterface;

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
}
