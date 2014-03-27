<?php

namespace BehatLauncher\Extension;

use BehatLauncher\Application;
use BehatLauncher\Exception\ExtensionManagerFrozenException;
use BehatLauncher\Extension\ExtensionInterface;
use BehatLauncher\Extension\Resource\ResourceWorkspace;

class ExtensionManager
{
    private $extensions = array();
    private $resourceWorkspace;

    /**
     * Adds an extension to the extension manager.
     *
     * @throws ExtensionManagerFrozenException
     */
    public function addExtension(ExtensionInterface $extension)
    {
        if ($this->resourceWorkspace !== null) {
            throw new \RuntimeException('Cannot add extension: resource workspace initialized.');
        }

        $this->extensions[] = $extension;

        return $this;
    }

    public function getResourceWorkspace()
    {
        if (null === $this->resourceWorkspace) {
            $this->resourceWorkspace = new ResourceWorkspace($this->getResourceDirectories());
        }

        return $this->resourceWorkspace;
    }

    private function getResourceDirectories()
    {
        $result = array();

        foreach ($this->extensions as $extension) {
            if (is_dir($dir = $extension->getDirectory().'/Resources')) {
                $result[] = $dir;
            }
        }

        return $result;
    }
}
