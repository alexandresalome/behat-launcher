<?php

namespace Alex\BehatLauncher\Behat;

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
        $this->outputFiles = new OutputFileList();
    }

    public function reset()
    {
        $this->startedAt   = null;
        $this->finishedAt  = null;
        $this->returnCode  = null;
        $this->outputFiles->reset();
    }

    /**
     * Returns the process to execute for this unit.
     *
     * @param Project $project
     *
     * @return Process
     */
    public function getProcess(Project $project)
    {
        $path = $project->getPath();
        $pb = new ProcessBuilder(array('php', $project->getBehatBin()));
        $pb->setWorkingDirectory($project->getPath());

        $feature = $project->getFeaturesPath().'/'.$this->feature;
        $pb->add($feature);
        $pb->setTimeout(null);

        return $pb->getProcess();
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

    public function setRun(Run $run = null)
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

    /**
     * Returns an OutputFile.
     *
     * @return OutputFile
     *
     * @throws InvalidArgumentException
     */
    public function getOutputFile($name)
    {
        return $this->outputFiles->get($name);
    }

    public function setOutputFile($name, $file)
    {
        $this->outputFiles->set($name, $file);

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
        file_put_contents($output = tempnam(sys_get_temp_dir(), 'bl_'), $process->getOutput());
        file_put_contents($error  = tempnam(sys_get_temp_dir(), 'bl_'), $process->getErrorOutput());
        $this->finishedAt = new \DateTime();
        $this->setOutputFile('_stdout', $output);
        $this->setOutputFile('_stderr', $error);
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
