<?php

namespace Alex\BehatLauncherBundle\Behat;

use Alex\BehatLauncherBundle\Behat\Storage\RunStorageInterface;

/**
 * Lazy-load run units from storage on call.
 */
class LazyRunUnitList extends RunUnitList
{
    /**
     * @var RunStorage
     */
    private $storage;

    /**
     * @var Run
     */
    private $run;

    /**
     * @var array
     */
    private $cache;
    private $cacheCount = array();

    /**
     * Creates a new lazy-loaded run unit list.
     *
     * @param RunStorage $storage a storage for fetching run units
     * @param Run        $run     the concerned run
     */
    public function __construct(RunStorageInterface $storage, Run $run)
    {
        $this->storage = $storage;
        $this->run = $run;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        if (null === $this->cache) {
            $this->cache = $this->storage->getUnits($this->run)->all();
        }

        return $this->cache;
    }

    /**
     * @return integer
     */
    public function setCount($count)
    {
        $this->cacheCount['all'] = $count;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        if (!isset($this->cacheCount['all'])) {
            $this->cacheCount['all'] = parent::count();
        }

        return $this->cacheCount['all'];
    }

    public function setCountPending($countPending)
    {
        $this->cacheCount['pending'] = $countPending;

        return $this;
    }

    public function countPending()
    {
        if (!isset($this->cacheCount['pending'])) {
            $this->cacheCount['pending'] = parent::countPending();
        }

        return $this->cacheCount['pending'];
    }

    public function setCountRunning($countRunning)
    {
        $this->cacheCount['running'] = $countRunning;

        return $this;
    }

    public function countRunning()
    {
        if (!isset($this->cacheCount['running'])) {
            $this->cacheCount['running'] = parent::countRunning();
        }

        return $this->cacheCount['running'];
    }

    public function setCountFinished($countFinished)
    {
        $this->cacheCount['finished'] = $countFinished;

        return $this;
    }

    public function setCountSucceeded($countFinished)
    {
        $this->cacheCount['finished'] = $countFinished;

        return $this;
    }

    public function countFinished()
    {
        if (!isset($this->cacheCount['finished'])) {
            $this->cacheCount['finished'] = parent::countFinished();
        }

        return $this->cacheCount['finished'];
    }
}
