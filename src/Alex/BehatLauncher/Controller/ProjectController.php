<?php

namespace Alex\BehatLauncher\Controller;

use Alex\BehatLauncher\Application;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends Controller
{
    public static function route(Application $app)
    {
        $app->get('/projects', 'controller.project:listAction')->bind('project_list');
        $app->get('/projects/{name}', 'controller.project:showAction')->bind('project_show');
    }

    public function listAction(Request $request)
    {
        $result = array();

        foreach ($this->getProjectList()->getAll() as $project) {
            $row = array(
                'name' => $project->getName(),
                'runs' => array(),
            );

            $runs = $this->getRunStorage()->getRuns($project, 0, 5);
            foreach ($runs as $run) {
                $row['runs'][] = $run;
            }

            $result[] = $row;
        }

        return $this->serialize($request, $result);
    }

    public function showAction($name)
    {
        $project = $this->getProjectList()->get($name);

        $result = array(
            'name' => $project->getName(),
            'runs' => array(),
            'features' => $project->getFeatures()->toArray()
        );

        $runs = $this->getRunStorage()->getRuns($project, 0, 5);
        foreach ($runs as $run) {
            $result['runs'][] = $run->toArray();
        }

        return $this->serialize($request, $result);
    }
}
