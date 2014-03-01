<?php

namespace Alex\BehatLauncher\Behat;

/**
 * A collection of projects.
 */
class ProjectList implements \IteratorAggregate
{
    /**
     * @var array
     */
    private $projects = array();

    /**
     * Creates a new project.
     *
     * @param array $projects an array of Project objects.
     */
    public function __construct(array $projects = array())
    {
        foreach ($projects as $project) {
            $this->add($project);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->projects);
    }

    /**
     * Adds a project to the list.
     *
     * @param Project $project project to add to list
     *
     * @return ProjectList
     */
    public function add(Project $project)
    {
        $this->projects[] = $project;

        return $this;
    }

    /**
     * Returns all projects.
     *
     * @return array an array of Project objects
     */
    public function getAll()
    {
        return $this->projects;
    }

    /**
     * Searches a Project in the list.
     *
     * @param string $name a project name
     *
     * @return Project
     *
     * @throws InvalidArgumentException project not found
     */
    public function get($name)
    {
        foreach ($this->projects as $project) {
            if ($project->getName() === $name) {
                return $project;
            }
        }

        throw new \InvalidArgumentException(sprintf('Project named "%s" not found. Available are: %s', $name, implode(', ', array_map(function ($p) {
            return $p->getNAme();
        }, $this->projects))));
    }
}
