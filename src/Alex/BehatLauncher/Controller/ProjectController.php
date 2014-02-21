<?php

namespace Alex\BehatLauncher\Controller;

class ProjectController extends Controller
{
    public function listAction()
    {
        $result = array();

        foreach ($this->getProjectList()->getAll() as $project) {
            $row = array(
                'name' => $project->getName(),
                'runs' => array(),
            );

            $runs = $this->getRunStorage()->getRuns($project, 0, 5);
            foreach ($runs as $run) {
                $row['runs'][] = $run->toArray();
            }

            $result[] = $row;
        }

        return json_encode($result);
    }

    public function showAction($name)
    {
        $project = $this->getProjectList()->get($name);

        $result = array(
            'name' => $project->getName(),
            'runs' => array(),
            'features' => $project->getFeatures()
        );

        $runs = $this->getRunStorage()->getRuns($project, 0, 5);
        foreach ($runs as $run) {
            $result['runs'][] = $run->toArray();
        }

        return json_encode($result);
    }
}
