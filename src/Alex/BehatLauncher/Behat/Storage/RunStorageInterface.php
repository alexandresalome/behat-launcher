<?php

namespace Alex\BehatLauncher\Behat\Storage;

use Alex\BehatLauncher\Behat\OutputFile;
use Alex\BehatLauncher\Behat\Project;
use Alex\BehatLauncher\Behat\Run;
use Alex\BehatLauncher\Behat\RunUnit;
use Alex\BehatLauncher\Behat\RunUnitList;

interface RunStorageInterface
{
    /**
     * Saves a Run in storage.
     *
     * @param Run $run run to persist
     *
     * @return RunStorageInterface
     */
    public function saveRun(Run $run);

    /**
     * Saves a Run in storage.
     *
     * @param Run $run run to persist
     *
     * @return RunStorageInterface
     */
    public function saveRunUnit(RunUnit $unit);

    /**
     * Returns run units associated to a given run.
     *
     * @param Run $run a run
     *
     * @return RunUnitList
     */
    public function getUnits(Run $run);

    /**
     * Returns a runnable unit.
     *
     * @param Project $project specific project to search in
     *
     * @return RunUnit
     */
    public function getRunnableUnit(Project $project = null);

    /**
     * Returns a given run
     *
     * @param string $id a run ID
     *
     * @return Run
     */
    public function getRun($id);

    /**
     * Searches for runs in a project.
     *
     * @param Project $project a project
     * @param int     $offset  pagination offset
     * @param int     $limit   pagination limit
     *
     * @return array
     */
    public function getRuns(Project $project = null, $offset = 0, $limit = 100);

    /**
     * Returns a new OutputFile.
     *
     * @return OutputFile
     */
    public function createOutputFile();

    /**
     * Returns absolute path to a file from an ID.
     *
     * @return OutputFile
     */
    public function getOutputFile($id);
}
