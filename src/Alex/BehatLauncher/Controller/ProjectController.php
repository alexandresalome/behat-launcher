<?php

namespace Alex\BehatLauncher\Controller;

class ProjectController extends Controller
{
    public function listAction()
    {
        return $this->render('Project/list.html.twig', array(
            'project_list' => $this->getProjectList(),
            'run_storage'  => $this->getRunStorage(),
        ));
    }
}
