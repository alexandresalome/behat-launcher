<?php

namespace Alex\BehatLauncher\Behat;

class OutputFileList implements \IteratorAggregate, \Countable
{
    private $pendingFiles = array();
    private $files;

    public function __construct(array $files = array())
    {
        $this->files = $files;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->files);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->files);
    }

    /**
     * @return string[]
     */
    public function getNames()
    {
        return array_keys($this->files);
    }

    /**
     * @return OutputFileList
     */
    public function set($name, $path)
    {
        $this->pendingFiles[$name] = $path;

        return $this;
    }

    /**
     * Returns an output file, given his name.
     *
     * @param string $name
     *
     * @throws InvalidArgumentException no file with given name
     */
    public function get($name)
    {
        if (!isset($this->files[$name])) {
            throw new \InvalidArgumentException(sprintf('No output file named "%s". Available are: %s.', $name, implode(', ', array_keys($this->files))));
        }

        return $this->files[$name];
    }

    /**
     * Persits the list to a storage
     */
    public function save(MysqlStorage $storage)
    {
        foreach ($this->pendingFiles as $name => $file) {
            $outputFile = $storage->createOutputFile();
            $outputFile->moveFrom($file);

            $this->files[$name] = $outputFile;
        }

        $this->pendingFiles = array();
    }

    /**
     * Add files to the list from an array of ID.
     *
     * @return OutputFileList
     */
    public function fromArrayOfID(MysqlStorage $storage, array $array)
    {
        foreach ($array as $name => $id) {
            $this->files[$name] = $storage->getOutputFile($id);
        }

        return $this;
    }

    /**
     * @return array an array of ID
     */
    public function toArrayOfID()
    {
        return array_map(function ($of) {
            return $of->getId();
        }, $this->files);
    }
}
