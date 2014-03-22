<?php

namespace Alex\BehatLauncher\Model;

use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Holds a collection of output files and provide methods around it.
 *
 * Files are stored in temporary folder until they're saved to a dedicated
 * folder.
 */
class OutputFileList implements \IteratorAggregate, \Countable, NormalizableInterface
{
    /**
     * @var array Collection of files
     */
    private $files;

    public function __construct(array $files = array())
    {
        $this->files = $files;
    }

    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = array())
    {
        return array_map(function ($of) {
            return $of->getId();
        }, array_filter($this->files, function ($of) {
            return !$of->isEmpty();
        }));
    }

    /**
     * Reinitialize the collection, removes all files.
     *
     * @return OutputFileList
     */
    public function reset()
    {
        foreach ($this->files as $file) {
            $file->delete();
        }

        $this->files = array();

        return $this;
    }

    /**
     * Appends a content to given format.
     *
     * @return OutputFile
     */
    public function append($format, $content)
    {
        return $this->get($format)->append($content);
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
     * Returns an output file, given his name.
     *
     * @param string $name
     *
     * @throws InvalidArgumentException no file with given name
     */
    public function get($format)
    {
        if (!isset($this->files[$format])) {
            return $this->files[$format] = new OutputFile();
        }

        return $this->files[$format];
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
