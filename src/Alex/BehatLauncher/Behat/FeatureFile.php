<?php

namespace Alex\BehatLauncher\Behat;

class FeatureFile
{
    private $directory;
    private $name;

    public function __construct(FeatureDirectory $directory, $name)
    {
        $this->directory = $directory;
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPath()
    {
        $parent = $this->directory->getPath();

        if ($parent === '') {
            return $this->name;
        }

        return $parent.'/'.$this->name;
    }

    public function toArray()
    {
        return array(
            'type' => 'file',
            'path' => $this->getPath(),
            'name' => $this->name
        );
    }

    public function getType()
    {
        return 'directory';
    }
}
