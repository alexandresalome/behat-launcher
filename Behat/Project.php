<?php

namespace Alex\BehatLauncherBundle\Behat;

use Symfony\Component\Finder\Finder;

/**
 * Project object
 */
class Project
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
     * @var array
     */
    private $properties = array();

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
     * Example return value:
     *
     *     array(
     *         'admin' => array('foo.feature', 'bar.feature')
     *         'user' => array('account.feature')
     *         'homepage.feature'
     *     )
     *
     * @return array an array structured with files and folders, as above.
     */
    public function getFeatures()
    {
        if (null === $this->path || !is_dir($this->path)) {
            throw new \RuntimeException(sprintf('Path "%s" does not exist.', $this->path));
        }

        $path = $this->path.'/features';
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

        return $this;
    }
}
