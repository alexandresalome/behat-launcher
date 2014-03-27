<?php

namespace BehatLauncher\Extension;

use BehatLauncher\Application;

interface ExtensionInterface
{
    /**
     * Register services in the application
     */
    public function register(Application $app);

    /**
     * Returns path on disk to the extension, containing classes and resources.
     *
     * @return string
     */
    public function getDirectory();

    /**
     * Returns extension namespace.
     *
     * @return string
     */
    public function getNamespace();
}
