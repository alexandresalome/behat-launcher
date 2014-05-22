<?php

namespace Alex\BehatLauncher\Behat;

use Alex\BehatLauncher\Behat\MysqlStorage;
use Alex\BehatLauncher\Behat\ProjectProperty;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Project object
 */
class Project implements NormalizableInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $featuresPath = 'features';

    /**
     * @var string
     */
    private $behatBin;

    /**
     * @var array
     */
    private $properties = array();

    /**
     * @var array
     */
    private $formats = array('pretty');

    /**
     * @var int
     */
    private $runnerCount = 1;

    /**
     * Creates a new Behat Launcher project, to execute runs on it.
     *
     * @param string $name project name
     * @param string $path path to the Behat project
     * @param array $properties an array of ProjectProperty objects
     */
    public function __construct($name = null, $path = null, array $properties = array())
    {
        $this->name = $name;
        $this->path = $path;

        foreach ($properties as $property) {
            $this->addProperty($property);
        }
    }

    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = array())
    {
        $result = array(
            'name'        => $this->name,
            'path'        => $this->path,
        );

        if (isset($context['project_details']) && $context['project_details']) {
            $result['properties'] = $normalizer->normalize($this->properties, $format, $context);
            $result['features']   = $normalizer->normalize($this->getFeatures(), $format, $context);
        }

        if (isset($context['project_runs']) && $context['project_runs']) {
            $maxRuns = isset($context['max_runs']) ? $context['max_runs'] : 5;
            $runs = $context['run_storage']->getRuns($this, 0, $maxRuns);
            $result['runs'] = $normalizer->normalize($runs, $format, $context);
        }

        return $result;
    }

    /**
     * Creates a run for the project.
     *
     * @return Run
     */
    public function createRun()
    {
        $run = new Run();
        $run
            ->setProjectName($this->name)
        ;

        return $run;
    }

    /**
     * Merges values with actual project configuration (from behat.yml).
     *
     * @return array
     */
    public function getConfig(array $values = array())
    {
        if (file_exists($file = $this->path.'/behat.yml')) {
            $content = Yaml::parse($file);
        } else {
            $content = array();
        }

        foreach ($this->getProperties() as $property) {
            $value = isset($values[$property->getName()]) ? $values[$property->getName()] : null;
            $content = $property->mergeConfig($content, $value);
        }

        return $content;
    }

    /**
     * @return FeatureDirectory
     */
    public function getFeatures()
    {
        if (null === $this->path || !is_dir($this->path)) {
            throw new \RuntimeException(sprintf('Path "%s" does not exist.', $this->path));
        }

        $path = $this->path.'/'.$this->getFeaturesPath();
        if (!is_dir($path)) {
            throw new \RuntimeException(sprintf('Folder "%s" does not exist.', $path));
        }

        $finder = new FeatureFinder();

        return $finder->findFeatures($path);
    }

    /**
     * Returns project's name.
     *
     * @return string project name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Changes project name.
     *
     * @param string $name new project name
     *
     * @return Project
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Change directory containing .features files.
     *
     * @return string
     */
    public function getFeaturesPath()
    {
        return $this->featuresPath;
    }

    /**
     * Returns directory containing .features files.
     *
     * @param string $featuresPath
     */
    public function setFeaturesPath($featuresPath)
    {
        $this->featuresPath = $featuresPath;
    }

    /**
     * Returns project path.
     *
     * @return string a path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Changes project's path.
     *
     * @param string $path a path to set
     *
     * @return Project
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Changes path to behat binary.
     *
     * @param string $behatBin
     *
     * @return Project
     */
    public function setBehatBin($behatBin)
    {
        $this->behatBin = $behatBin;

        return $this;
    }

    /**
     * Returns path to behat binary
     *
     * @return string
     */
    public function getBehatBin()
    {
        if (null !== $this->behatBin) {
            return $this->behatBin;
        }

        $possiblePaths = array(
            $this->path.'/bin/behat',
            $this->path.'/vendor/behat/behat/bin/behat'
        );

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        throw new \RuntimeException(sprintf('Unable to find Behat path in %s.', implode(', ', $possiblePaths)));
    }

    /**
     * Returns project's properties.
     *
     * @return array an array of ProjectProperty
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Changes all project's properties.
     *
     * @param array $properties an array of ProjectProperty objects.
     *
     * @return Project
     */
    public function setProperties(array $properties)
    {
        $this->properties = array();
        foreach ($properties as $property) {
            $this->addProperty($property);
        }

        return $this;
    }

    /**
     * Adds a property to the project.
     *
     * @param ProjectProperty $property a project's property
     *
     * @return Project
     */
    public function addProperty(ProjectProperty $property)
    {
        $this->properties[] = $property;
        $property->setProject($this);

        return $this;
    }

    public function createProperty($name)
    {
        $property = new ProjectProperty($this, $name);
        $this->addProperty($property);

        return $property;
    }

    /**
     * @param string $format a behat format (pretty, html, failed)
     *
     * @return Project
     */
    public function addFormat($format)
    {
        $this->formats[] = $format;

        return $this;
    }

    /**
     * @param string $format a behat format (pretty, html, failed)
     *
     * @return Project
     */
    public function setFormats(array $formats)
    {
        $this->formats = $formats;

        return $this;
    }

    /**
     * @return array an array of string, behat format (pretty, html, failed)
     */
    public function getFormats()
    {
        return $this->formats;
    }

    /**
     * Returns number of parallel processes a project can have.
     *
     * @return integer
     */
    public function getRunnerCount()
    {
        return $this->runnerCount;
    }

    /**
     * Changes number of parallel processes a project can have.
     *
     * @param integer $runnerCount
     *
     * @return Project
     */
    public function setRunnerCount($runnerCount)
    {
        $this->runnerCount = $runnerCount;

        return $this;
    }
}
