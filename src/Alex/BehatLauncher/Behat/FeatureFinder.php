<?php

namespace Alex\BehatLauncher\Behat;

use Symfony\Component\Finder\Finder;

class FeatureFinder
{
    public function findFeatures($path, $contentFilters = null)
    {
        if ($contentFilters == null)
        {
            $finder = Finder::create()
                ->sortByName()
                ->in($path)
                ->name('*.feature')
            ;
        }
        else
        {
            $finder = Finder::create()
                ->sortByName()
                ->in($path)
                ->name('*.feature')
                ->contains($contentFilters)
            ;
        }

        $result = new FeatureDirectory('');

        foreach ($finder as $file) {
            $feature = (string) $file;
            if (0 !== strpos($feature, $path)) {
                throw new \LogicException(sprintf('Feature resolved out of path: %s (out of %s).', $feature, $path));
            }
            $feature = substr($feature, strlen($path) + 1);
            $feature = str_replace('\\', '/', $feature);

            $this->addToDirectory($result, $feature);
        }

        return $result;
    }

    private function addToDirectory(FeatureDirectory $directory, $feature)
    {
        if (false === strpos($feature, '/')) {
            $directory->addEntry(new FeatureFile($directory, $feature));

            return;
        }

        list($name, $rest) = explode('/', $feature, 2);
        $this->addToDirectory($directory->getOrCreateDirectory($name), $rest);
    }
}
