<?php

namespace Alex\BehatLauncher\Behat;

/**
 * A collection of run units.
 */
class RunUnitList implements \IteratorAggregate, \Countable
{
    private $units;

    public function __construct(array $units = array())
    {
        $this->units = $units;
    }

    /**
     * Returns all units as a PHP array.
     *
     * @return array
     */
    public function all()
    {
        return $this->units;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->all());
    }

    /**
     * Fetches a unit by ID.
     *
     * @param string $id a unit ID
     *
     * @return RunUnit unit with given id
     *
     * @throws InvalidArgumentException No unit found in this run with given ID
     */
    public function get($id)
    {
        foreach ($this->units as $unit) {
            if ($unit->getId() === $id) {
                return $unit;
            }
        }

        throw new \InvalidArgumentException(sprintf('No unit found with ID "%s".', $id));
    }

    /**
     * Adds a run unit to the list.
     *
     * @param RunUnit $unit a run unit
     *
     * @return Run
     */
    public function add(RunUnit $unit)
    {
        $this->units[] = $unit;

        return $this;
    }

    /**
     * @return integer
     */
    public function countPending()
    {
        $count = 0;
        foreach ($this->all() as $one) {
            if ($one->isPending()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @return boolean
     */
    public function isPending()
    {
        foreach ($this->all() as $one) {
            if (!$one->isPending()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return integer
     */
    public function countRunning()
    {
        $count = 0;
        foreach ($this->all() as $one) {
            if ($one->isRunning()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @return boolean
     */
    public function isRunning()
    {
        foreach ($this->all() as $one) {
            if ($one->isRunning()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return integer
     */
    public function countFailed()
    {
        $count = 0;
        foreach ($this->all() as $one) {
            if ($one->isFailed()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @return boolean
     */
    public function isFailed()
    {
        foreach ($this->all() as $one) {
            if (!$one->isFinished()) {
                return false;
            }

            if ($one->isFailed()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return integer
     */
    public function countSucceeded()
    {
        $count = 0;
        foreach ($this->all() as $one) {
            if ($one->isSucceeded()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @return boolean
     */
    public function isSucceeded()
    {
        foreach ($this->all() as $one) {
            if (!$one->isFinished()) {
                return false;
            }

            if (!$one->isSucceeded()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return integer
     */
    public function countFinished()
    {
        $count = 0;
        foreach ($this->all() as $one) {
            if ($one->isFinished()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @return boolean
     */
    public function isFinished()
    {
        foreach ($this->all() as $one) {
            if (!$one->isFinished()) {
                return false;
            }
        }

        return true;
    }
}
