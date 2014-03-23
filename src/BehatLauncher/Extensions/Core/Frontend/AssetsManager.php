<?php

namespace BehatLauncher\Extensions\Core\Frontend;

class AssetsManager
{
    private $dirs = array();
    private $targetDir;

    public function setTargetDir($targetDir)
    {
        $this->targetDir = $targetDir;

        return $this;
    }

    public function addResourcesDirs($dirs)
    {
        foreach ($dirs as $dir) {
            $this->addResourcesDir($dir);
        }

        return $this;
    }

    public function addResourcesDir($dir)
    {
        $this->dirs[] = $dir;

        return $this;
    }

    public function getJavascriptContent()
    {
        die('lol');
    }

    public function getCssContent()
    {
        var_dump($this->dirs);exit;
    }
}
