<?php

namespace Alex\BehatLauncher\Extension\Core\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class Run implements NormalizableInterface, DenormalizableInterface
{
    private $id;
    private $title;
    private $projectName;
    private $properties = array();
    private $createdAt;
    private $startedAt;
    private $finishedAt;

    /**
     * @var RunUnitList
     */
    private $units;

    public function __construct()
    {
        $this->units = new RunUnitList();
        $this->createdAt = new \DateTime();
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = array())
    {
        if (isset($data['title'])) {
            $this->setTitle($data['title']);
        }

        if (isset($data['projectName'])) {
            $this->setProjectName($data['projectName']);
        }

        if (isset($data['properties'])) {
            $this->setProperties($data['properties']);
        }

        if (isset($data['features'])) {
            $this->createUnits($data['features']);
        }

        return $this;
    }

    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = array())
    {
        $result = array(
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'projectName' => $this->getProjectName(),
            'status' => $this->getStatus(),
            'running' => $this->isRunning(),
            'properties' => $this->getProperties(),
            'count' => array(
                'pending'   => $this->countStatus('pending'),
                'running'   => $this->countStatus('running'),
                'succeeded' => $this->countStatus('succeeded'),
                'failed'    => $this->countStatus('failed'),
            ),
            'progress' => $this->getProgress()
        );

        if (isset($context['run_details']) && $context['run_details']) {
            $result['units'] = $normalizer->normalize($this->getUnits());
        }

        return $result;
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
    public function createUnits(array $features)
    {
        foreach ($features as $feature) {
            $unit = new RunUnit();
            $unit->setFeature($feature);
            $unit->setRun($this);
            $this->units->add($unit);
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
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return Run
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;

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
     * @return DateInterval
     */
    public function getDuration()
    {
        if (!$this->isFinished()) {
            throw new \LogicException('Cannot compute duration: run is not finished.');
        }

        return $this->finishedAt->diff($this->startedAt);
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
     * @param string $status a status (pending, running, succeeded, failed)
     *
     * @return int
     */
    public function countStatus($status)
    {
        if ($status === 'pending') {
            return $this->countPending();
        } elseif ($status === 'running') {
            return $this->countRunning();
        } elseif ($status === 'succeeded') {
            return $this->countSucceeded();
        } elseif ($status === 'failed') {
            return $this->countFailed();
        }

        throw new \InvalidArgumentException(sprintf('Status "%s" seems not to be a good status.', $status));
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

    /**
     * Returns an array of status progress, as integers.
     *
     * @param string $count number of count to distribute
     *
     * @return int[]
     */
    public function getProgress($count = 100)
    {
        $counters = array(
            'pending'   => $this->countPending(),
            'running'   => $this->countRunning(),
            'succeeded' => $this->countSucceeded(),
            'failed'    => $this->countFailed(),
        );

        $total = array_sum($counters);
        $ratios = array_map(function ($x) use ($total, $count) {
            if ($total === 0) {
                return 0;
            }

            return floor($x*$count/$total);
        }, $counters);
        $extra = $count - array_sum($ratios);

        reset($counters);
        while ($extra > 0 && key($counters)) {
            if ($ratios[key($counters)]) {
                $ratios[key($counters)]++;
                $extra--;
            }
            next($counters);
        }

        return $ratios;
    }
}
