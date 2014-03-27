<?php

namespace BehatLauncher\Extension\Resource;

use Symfony\Component\Finder\Finder;

class ResourceWorkspace
{
    private $directories;

    public function __construct(array $directories)
    {
        $this->directories = $directories;
    }

    public function getMergedFiles($glob)
    {
        $finder = Finder::create()->in($this->directories)->path($glob)->files();

        $result = array();
        foreach ($finder as $file) {
            $result[$file->getRelativePathname()] = $file->getPathname();
        }

        return $result;
    }

    public function getDirectories($dirname = '')
    {
        if ($dirname === '') {
            return $this->directories;
        }

        $result = array();
        foreach ($this->directories as $directory) {
            if (is_dir($dir = $directory.'/'.$dirname)) {
                $result[] = $dir;
            }
        }

        return $result;
    }
}
