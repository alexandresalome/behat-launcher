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
        return $this->serialize($this->getProjectList()->getAll());
    }

    public function showAction(Request $request, $name)
    {
        return $this->serialize($this->getProjectList()->get($name));
    }
}
