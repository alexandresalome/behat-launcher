<?php

namespace Alex\BehatLauncherBundle\Controller;

use Alex\WebBundle\Controller\Controller;

class ProjectController extends Controller
{
    public function listAction()
    {
        return $this->render('AlexBehatLauncherBundle:Project:list.html.twig', array(
            'project_list' => $this->get('behat_launcher.project_list'),
            'run_storage'  => $this->get('behat_launcher.run_storage')
        ));
    }
}
