<?php

namespace Alex\BehatLauncherBundle\Behat;

use Alex\BehatLauncherBundle\Behat\Project;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class RunUnit
{
    private $id;
    private $run;
    private $feature;
    private $createdAt;
    private $startedAt;
    private $finishedAt;
    private $returnCode;
    private $outputFiles;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getProcess(Project $project)
    {
        $path = $project->getPath();
        $pb = new ProcessBuilder(array('php', $this->findBehatBinary($path)));

        $feature = $project->getFeaturesPath().'/'.$this->feature;
        $pb->add($feature);

        return $pb->getProcess();
    }

    private function findBehatBinary($path)
    {
        $possiblePaths = array(
            $path.'/bin/behat',
            $path.'/vendor/behat/behat/bin/behat'
        );

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        throw new \RuntimeException(sprintf('Unable to find Behat path in %s.', implode(', ', $possiblePaths)));
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getRun()
    {
        return $this->run;
    }

    public function setRun(Run $run)
    {
        $this->run = $run;

        return $this;
    }

    public function getFeature()
    {
        return $this->feature;
    }

    public function setFeature($feature)
    {
        $this->feature = $feature;

        return $this;
    }

    public function getReturnCode()
    {
        return $this->returnCode;
    }

    public function setReturnCode($returnCode)
    {
        $this->returnCode = $returnCode;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isPending()
    {
        return null === $this->startedAt;
    }

    /**
     * @return boolean
     */
    public function isRunning()
    {
        return null !== $this->startedAt && null === $this->finishedAt;
    }

    /**
     * @return boolean
     */
    public function isFinished()
    {
        return null !== $this->finishedAt;
    }

    /**
     * @return boolean
     */
    public function isFailed()
    {
        return $this->isFinished() && $this->returnCode != 0;
    }

    /**
     * @return boolean
     */
    public function isSucceeded()
    {
        return $this->isFinished() && $this->returnCode == 0;
    }

    public function getOutputFiles()
    {
        return $this->outputFiles;
    }

    public function setOutputFiles($outputFiles)
    {
        $this->outputFiles = $outputFiles;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStartedAt()
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTime $startedAt = null)
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * @return RunUnit
     *
     * @throws LogicException already started
     */
    public function start()
    {
        if (null !== $this->startedAt) {
            throw new \LogicException('Run unit already started.');
        }

        $this->startedAt = new \DateTime();

        return $this;
    }

    public function finish(Process $process)
    {
        if (null !== $this->finishedAt) {
            throw new \LogicException('Run unit already finished.');
        }

        $this->returnCode = $process->getExitCode();
        $this->finishedAt = new \DateTime();

    }

    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(\DateTime $finishedAt = null)
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    /**
     * @return string
     */
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
}
