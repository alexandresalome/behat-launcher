<?php

namespace Alex\BehatLauncher\Behat;

class FeatureDirectory implements \IteratorAggregate, \Countable
{
    private $parent;
    private $name;
    private $entries = array();

    public function __construct($name, FeatureDirectory $parent = null)
    {
        $this->name    = $name;
        $this->parent  = $parent;
        $this->entries = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->entries);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->entries);
    }

    public function getOrCreateDirectory($name)
    {
        if (!$this->hasDirectory($name)) {
            $sub = new FeatureDirectory($name, $this);
            $this->addEntry($sub);
        } else {
            $sub = $this->getDirectory($name);
        }

        return $sub;
    }

    public function getDirectory($name)
    {
        foreach ($this->entries as $entry) {
            if ($entry instanceof FeatureDirectory && $entry->getName() === $name) {
                return $entry;
            }
        }

        throw new \InvalidArgumentException(sprintf('No directory named "%s" in "%s".', $name, $this->getPath()));
    }

    public function hasDirectory($name)
    {
        foreach ($this->entries as $entry) {
            if ($entry->getName() == $name) {
                return true;
            }
        }

        return false;
    }

    public function addEntry($entry)
    {
        if (!$entry instanceof FeatureDirectory && !$entry instanceof FeatureFile) {
            throw new \InvalidArgumentException(sprintf('Expected FeatureDirectory or FeatureFile, got "%s".', is_object($entry) ? get_class($entry) : gettype($entry)));
        }

        $this->entries[] = $entry;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getPath()
    {
        if (null === $this->parent) {
            return '';
        }

        $parent = $this->parent->getPath();
        if ($parent === '') {
            return $this->name;
        }

        return $parent.'/'.$this->name;
    }

    public function toArray()
    {
        return array(
            'type' => 'directory',
            'path' => $this->getPath(),
            'name' => $this->name,
            'entries' => array_map(function ($entry) {
                return $entry->toArray();
            }, $this->entries)
        );
    }

    public function getType()
    {
        return 'directory';
    }
}
