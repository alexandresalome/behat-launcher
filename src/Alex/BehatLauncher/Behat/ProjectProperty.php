<?php

namespace Alex\BehatLauncher\Behat;

use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProjectProperty implements NormalizableInterface
{
    private $project;
    private $name;
    private $config;
    private $env;
    private $formType;
    private $formOptions;

    public function __construct(Project $project = null, $name = null)
    {
        $this->project = $project;
        $this->name = $name;
    }

    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = array())
    {
        return array(
            'name'        => $this->name,
            'config'      => $this->config,
            'env'         => $this->env,
            'formType'    => $this->formType,
            'formOptions' => $this->formOptions
        );
    }

    public function mergeConfig(array $config, $value)
    {
        if ($this->config) {
            $config = $this->doMergeConfig($config, $value, $this->config);
        }

        return $config;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function setProject(Project $project)
    {
        $this->project = $project;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    public function getEnv()
    {
        return $this->env;
    }

    public function setEnv($env)
    {
        $this->env = $env;

        return $this;
    }

    public function getFormType()
    {
        return $this->formType;
    }

    public function setFormType($formType)
    {
        $this->formType = $formType;

        return $this;
    }

    public function getFormOptions()
    {
        return $this->formOptions;
    }

    public function setFormOptions(array $formOptions)
    {
        $this->formOptions = $formOptions;

        return $this;
    }

    public function endProperty()
    {
        return $this->project;
    }

    private function doMergeConfig(array $config, $value, $path)
    {
        if (false === $pos = strpos($path, '.')) {
            $config[$path] = $value;

            return $config;
        }

        $prefix = substr($path, 0, $pos);
        $suffix = substr($path, $pos + 1);

        if (!isset($config[$prefix])) {
            $config[$prefix] = $this->doMergeConfig(array(), $value, $suffix);

            return $config;
        }

        if (is_array($config[$prefix])) {
            $config[$prefix] = $this->doMergeConfig($config[$prefix], $value, $suffix);

            return $config;
        }

        throw new \RuntimeException(sprintf('Error at configuration key "%s": expected an array or empty, got "%s".', $prefix, $config[$prefix]));
    }
}
