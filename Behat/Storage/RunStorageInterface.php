<?php

namespace Alex\BehatLauncherBundle\Behat\Storage;

use Alex\BehatLauncherBundle\Behat\Project;
use Alex\BehatLauncherBundle\Behat\Run;
use Alex\BehatLauncherBundle\Behat\RunUnit;
use Alex\BehatLauncherBundle\Behat\RunUnitList;

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
}
