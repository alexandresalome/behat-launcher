<?php

namespace Alex\BehatLauncher\Behat;

use Symfony\Component\Finder\Finder;

class FeatureFinder
{
    public function findFeatures($path)
    {
        $result = array();

        $finder = Finder::create()
            ->sortByName()
            ->in($path)
            ->name('*.feature')
        ;

        $result = array();

        foreach ($finder as $file) {
            $feature = (string) $file;
            if (0 !== strpos($feature, $path)) {
                throw new \LogicException(sprintf('Feature resolved out of path: %s (out of %s).', $feature, $path));
            }
            $feature = substr($feature, strlen($path) + 1);
            $feature = str_replace('\\', '/', $feature);

            $this->addToResult($feature, $result);
        }

        return $result;
    }

    private function addToResult($feature, &$result)
    {
        if (false === strpos($feature, '/')) {
            $result[] = $feature;

            return;
        }

        list($name, $rest) = explode('/', $feature, 2);
        if (!isset($result[$name])) {
            $result[$name] = array();
        }
        $this->addToResult($rest, $result[$name]);
    }
}
