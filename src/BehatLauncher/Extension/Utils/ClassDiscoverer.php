<?php

namespace BehatLauncher\Extension\Utils;

use Symfony\Component\Finder\Finder;

/**
 * A class to find class in files and strings
 */
class ClassDiscoverer
{
    /**
     * Searches class in given subject.
     *
     * Supports:
     *
     * - Finder
     * - a file path
     * - a source string
     *
     * @param Finder|string $subject
     *
     * @return string[] an array of class names.
     *
     * @throws InvalidArgumentException If unknown subject
     */
    public function discover($subject)
    {
        if ($subject instanceof Finder) {
            return $this->discoverFinder($subject);
        } elseif (is_string($subject) && file_exists($subject)) {
            return $this->discoverFile($subject);
        } elseif (is_string($subject)) {
            return $this->discoverString($subject);
        }

        throw new \InvalidArgumentException(sprintf('Expected a Finder or a string, got a "%s".', is_object($subject) ? get_class($subject) : gettype($subject)));
    }

    /**
     * Searches class in a Symfony Finder.
     *
     * @param Finder $finder a finder instance
     *
     * @return string[] an array of class names.
     */
    public function discoverFinder(Finder $finder)
    {
        $classes = array();

        foreach ($finder as $file) {
            if (is_dir($file)) {
                continue;
            }

            $classes = array_merge($classes, $this->discoverFile($file));
        }

        return $classes;
    }

    /**
     * Searches class in a file.
     *
     * @return  string[] an array of class names
     */
    public function discoverFile($file)
    {
        $content = file_get_contents($file);

        return $this->discoverString($content);
    }

    /**
     * Searches class in a string.
     *
     * @return  string[] an array of class names
     */
    public function discoverString($content)
    {
        preg_match_all('/(class\s+[^\s]+|namespace\s+[^;]+)/', $content, $vars);
        $classes = array();

        $currNamespace = null;
        for ($i = 0, $count = count($vars[1]); $i < $count; $i++) {
            if (0 === strpos($vars[1][$i], 'namespace')) {
                $currNamespace = trim(substr($vars[1][$i], 10));
            } else {
                $className = trim(substr($vars[1][$i], 6));
                $classes[] = $currNamespace ? $currNamespace.'\\'.$className : $className;
            }
        }

        return $classes;
    }
}
