<?php

namespace Alex\BehatLauncher\Extension;

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

    /**
     * {@inheritdoc}
     */
    public function getControllers()
    {
        return array();
    }

    private function getDir()
    {
        $refl = new \ReflectionClass($this);
        return dirname($refl->getFilename());
    }
}
