<?php

namespace Alex\BehatLauncher\Controller;

class ApiController extends Controller
{
    public function projectListAction()
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

    public function runListAction()
    {
        $result = array();

        foreach ($this->getRunStorage()->getRuns(null) as $run) {
            $result[] = $run->toArray();
        }

        return json_encode($result);
    }
}
