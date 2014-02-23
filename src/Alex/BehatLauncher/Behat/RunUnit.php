<?php

namespace Alex\BehatLauncher\Behat;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Yaml\Yaml;

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

    private $process;
    private $onFinish;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->outputFiles = new OutputFileList();
    }

    public function reset()
    {
        if ($this->process) {
            try {
                $this->process->stop();
            } catch (\Exception $e) {
                // ignore, we're trying to reset
            }
        }

        if ($this->onFinish) {
            call_user_func($this->onFinish);
        }

        $this->startedAt   = null;
        $this->finishedAt  = null;
        $this->returnCode  = null;
        $this->process     = null;
        $this->onFinish    = null;
        $this->outputFiles->reset();
    }

    /**
     * @return boolean a boolean indicating if unit's process is finished.
     */
    public function isProcessFinished()
    {
        if (null === $this->process) {
            throw new \LogicException('Process not started');
        }

        if ($this->process->isTerminated()) {
            call_user_func($this->onFinish);

            $this->process = null;
            $this->onFinish = null;

            return true;
        }

        return false;
    }

    /**
     * Starts the unit execution
     *
     * @param Project $project
     *
     * @return Process
     */
    public function start(Project $project, \Closure $output)
    {
        $path = $project->getPath();

        do {
            $tmp = 'bl_'.md5(uniqid().microtime(true));
            $configFile = $path.'/'.$tmp;
        } while (file_exists($configFile));

        $config = $project->getConfig($this->getRun()->getProperties());
        file_put_contents($configFile, Yaml::dump($config));

        $pb = new ProcessBuilder(array('php', $project->getBehatBin()));
        $pb->setWorkingDirectory($project->getPath());

        $pb->add('-c')->add($configFile);

        $feature = $project->getFeaturesPath().'/'.$this->feature;
        $pb->add($feature);
        $pb->setTimeout(null);

        $formats = $project->getFormats();

        $outputFiles = array();
        if (count($formats)) {
            $argFormats  = array();
            $argOutputs  = array();

            foreach ($formats as $format) {
                $argFormats[] = $format;
                $outputFiles[$format] = tempnam(sys_get_temp_dir(), 'bl_');
                $argOutputs[] = $outputFiles[$format];
            }

            $argFormats[] = 'pretty';
            $argOutputs[] = 'null';

            $pb->add('-f')->add(implode(',', $argFormats));
            $pb->add('--out')->add(implode(',', $argOutputs));
        }

        $pb->add('--ansi');

        $this->process = $pb->getProcess();
        $this->process->start(function ($type, $text) use ($output) {
            $output($text, $type != 'out');
        });

        $this->onFinish = function () use ($configFile, $outputFiles) {
            unlink($configFile);

            $this->returnCode = $this->process->getExitCode();
            file_put_contents($output = tempnam(sys_get_temp_dir(), 'bl_'), $this->process->getOutput());
            file_put_contents($error  = tempnam(sys_get_temp_dir(), 'bl_'), $this->process->getErrorOutput());
            $this->finishedAt = new \DateTime();
            $this->setOutputFile('_stdout', $output);
            $this->setOutputFile('_stderr', $error);

            foreach ($outputFiles as $format => $file) {
                $this->setOutputFile($format, $file);
            }
        };
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

    public function getDuration()
    {
        if (!$this->isFinished()) {
            throw new \LogicException('Cannot return duration: unit not finished.');
        }

        return $this->finishedAt->diff($this->startedAt);
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
