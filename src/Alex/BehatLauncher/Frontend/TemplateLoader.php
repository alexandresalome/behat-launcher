<?php

namespace Alex\BehatLauncher\Frontend;

use Symfony\Component\Finder\Finder;

class TemplateLoader
{
    /**
     * @var  string[] an array of directories
     */
    private $directories = array();

    /**
     * @var  string[]|null associative array of template file location, or null if not loaded yet
     */
    private $files       = null;

    public function addDirectory($directory)
    {
        $this->directories[] = $directory;

        return $this;
    }

    /**
     * @var  string[] a list of directories
     */
    public function addDirectories(array $directories)
    {
        foreach ($directories as $directory) {
            $this->addDirectory($directory);
        }

        return $this;
    }

    /**
     * Returns source for a given template.
     *
     * @param  string $name a template name
     *
     * @return  string a template source
     *
     * @throws InvalidArgumentException If template not found
     */
    public function getSources()
    {
        $this->update();

        return array_map('file_get_contents', $this->files);
    }

    /**
     * Updates the template loader file cache.
     *
     * @return  TemplateLoader fluid interface
     */
    public function update($force = false)
    {
        if ($this->files !== null || $force) {
            $this->update();
        }

        $this->files = array();

        foreach ($this->directories as $directory) {
            $finder = Finder::create()
                ->sortByName()
                ->in($directory)
                ->name('*.html')
            ;

            foreach ($finder as $file) {
                $current = (string) $file;
                if (0 !== strpos($current, $directory)) {
                    throw new \LogicException(sprintf('File resolved out of directory: %s (out of %s).', $current, $directory));
                }
                $current = substr($current, strlen($directory) + 1);
                $current = str_replace('\\', '/', $current);

                $this->files[$current] = (string) $file;
            }
        }

        return $this;
    }
}
