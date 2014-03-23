<?php

namespace BehatLauncher\Extension;

use BehatLauncher\Application;

interface ExtensionInterface
{
    /**
     * Returns path to Doctrine entities.
     *
     * @return string|false false if no directory
     */
    public function getModelPath();

    /**
     * Returns list of controller classes.
     *
     * @return string[]
     */
    public function getControllers();

    /**
     * Register services in the application
     */
    public function register(Application $app);

    public function getTwigTemplatesDir();

    public function getAngularTemplatesDir();

    public function getResourcesDir();
}
