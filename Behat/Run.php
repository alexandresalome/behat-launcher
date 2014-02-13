<?php

namespace Alex\BehatLauncherBundle\Behat;

class Run
{
    private $id;
    private $title;
    private $projectName;
    private $properties = array();
    private $startedAt;
    private $finishedAt;

    /**
     * @var RunUnitList
     */
    private $units;

    public function __construct()
    {
        $this->units = new RunUnitList();
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Changes the unit collection.
     *
     * @param RunUnitList $units units to replace with
     *
     * @return Run
     */
    public function setUnits(RunUnitList $units)
    {
        $this->units = $units;

        return $this;
    }

    /**
     * Creates run units
     */
    public function createUnits(array $features, $currentPath = '')
    {
        foreach ($features as $key => $value) {
            $path = $currentPath === '' ? $key : $currentPath.'/'.$key;
            if (is_array($value)) {
                $this->createUnits($value, $path);
            } elseif ($value) {
                $unit = new RunUnit();
                $unit->setFeature($path);
                $unit->setRun($this);
                $this->units->add($unit);
            }
        }
    }

    /**
     * @return RunUnitList
     */
    public function getUnits()
    {
        return $this->units;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Changes ID.
     *
     * @param string $id
     *
     * @return Run
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Returns project's name.
     *
     * @return string
     */
    public function getProjectName()
    {
        return $this->projectName;
    }

    /**
     * Changes the project name.
     *
     * @param string $projectName a project name
     *
     * @return Run
     */
    public function setProjectName($projectName)
    {
        $this->projectName = $projectName;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * @return Run
     */
    public function setStartedAt(\DateTime $startedAt = null)
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    /**
     * @return Run
     */
    public function setFinishedAt(\DateTime $finishedAt = null)
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    /**
     * @return Run
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    public function getStatus()
    {
        if ($this->isPending()) {
            return 'pending';
        }

        if ($this->isRunning()) {
            return 'running';
        }

        if ($this->isSucceeded()) {
            return 'succeeded';
        }

        if ($this->isFailed()) {
            return 'failed';
        }
    }

    /**
     * Returns a property value.
     *
     * @param string $name a property name
     *
     * @return mixed
     *
     * @throws InvalidArgumentException no property with such name
     */
    public function getProperty($name)
    {
        if (!isset($this->properties[$name])) {
            throw new \InvalidArgumentException(sprintf('No property "%s" on this run. Available are: %s.', $name, implode(', ', array_keys($this->properties))));
        }

        return $this->properties[$name];
    }

    /**
     * Changes a run property.
     *
     * @param string $name  the property to change
     * @param mixed  $value the value to set
     *
     * @return Run
     */
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;

        return $this;
    }

    /**
     * @return integer
     */
    public function countPending()
    {
        return $this->units->countPending();
    }

    /**
     * @return boolean
     */
    public function isPending()
    {
        return $this->units->isPending();
    }

    /**
     * @return integer
     */
    public function countRunning()
    {
        return $this->units->countRunning();
    }

    /**
     * @return boolean
     */
    public function isRunning()
    {
        return $this->units->isRunning();
    }

    /**
     * @return integer
     */
    public function countSucceeded()
    {
        return $this->units->countSucceeded();
    }

    /**
     * @return integer
     */
    public function isSucceeded()
    {
        return $this->units->isSucceeded();
    }

    /**
     * @return integer
     */
    public function countFailed()
    {
        return $this->units->countFailed();
    }

    /**
     * @return integer
     */
    public function isFailed()
    {
        return $this->units->isFailed();
    }

    /**
     * @return integer
     */
    public function countFinished()
    {
        return $this->units->countFinished();
    }

    /**
     * @return integer
     */
    public function isFinished()
    {
        return $this->units->isFinished();
    }
}
