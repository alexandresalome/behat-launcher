<?php

namespace BehatLauncher\Extensions\Core\Controller;

use BehatLauncher\Application;
use BehatLauncher\Extension\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends AbstractController
{
    public static function route(Application $app)
    {
        $id = self::id();
        $app->get('/projects', $id.':listAction')->bind('project_list');
        $app->get('/projects/{name}', $id.':showAction')->bind('project_show');
    }

    public function listAction(Request $request)
    {
        return $this->serialize($this->getProjectList()->getAll(), array('project_runs' => true));
    }

    public function showAction(Request $request, $name)
    {
        return $this->serialize($this->getProjectList()->get($name), array('project_details' => true, 'project_runs' => true));
    }
}
