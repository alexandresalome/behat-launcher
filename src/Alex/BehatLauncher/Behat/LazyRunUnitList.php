<?php

namespace Alex\BehatLauncher\Behat;

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
    public function __construct(MysqlStorage $storage, Run $run, $pending, $running, $succeeded, $failed)
    {
        $this->storage = $storage;
        $this->run = $run;

        $this->cacheCount['all']       = $all = $pending + $running + $succeeded + $failed;
        $this->cacheCount['pending']   = $pending;
        $this->cacheCount['running']   = $running;
        $this->cacheCount['succeeded'] = $succeeded;
        $this->cacheCount['failed']    = $failed;
        $this->cacheCount['finished']  = $finished = $succeeded + $failed;

        $this->cacheCount['is_pending'] = $pending && !$running;
        $this->cacheCount['is_running'] = $running > 0;
        $this->cacheCount['is_succeeded'] = $all == $succeeded;
        $this->cacheCount['is_failed'] = $all == $finished && $failed > 0;
        $this->cacheCount['is_finished'] = $all == $finished;
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
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->cacheCount['all'];
    }

    /**
     * {@inheritdoc}
     */
    public function countPending()
    {
        return $this->cacheCount['pending'];
    }

    /**
     * {@inheritdoc}
     */
    public function isPending()
    {
        return $this->cacheCount['is_pending'];
    }

    /**
     * {@inheritdoc}
     */
    public function countRunning()
    {
        return $this->cacheCount['running'];
    }

    /**
     * {@inheritdoc}
     */
    public function isRunning()
    {
        return $this->cacheCount['is_running'];
    }

    /**
     * {@inheritdoc}
     */
    public function countSucceeded()
    {
        return $this->cacheCount['succeeded'];
    }

    /**
     * {@inheritdoc}
     */
    public function isSucceeded()
    {
        return $this->cacheCount['is_succeeded'];
    }

    /**
     * {@inheritdoc}
     */
    public function countFailed()
    {
        return $this->cacheCount['failed'];
    }

    /**
     * {@inheritdoc}
     */
    public function isFailed()
    {
        return $this->cacheCount['is_failed'];
    }

    /**
     * {@inheritdoc}
     */
    public function countFinished()
    {
        return $this->cacheCount['finished'];
    }

    /**
     * {@inheritdoc}
     */
    public function isFinished()
    {
        return $this->cacheCount['is_finished'];
    }

    /**
     * {@inheritdoc}
     */
    public function add(RunUnit $unit)
    {
        throw new \LogicException('Cannot add a RunUnit to a LazyRunUnitList');
    }
}
