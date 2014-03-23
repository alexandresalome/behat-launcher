<?php

namespace Alex\BehatLauncher\Extension;

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
}
