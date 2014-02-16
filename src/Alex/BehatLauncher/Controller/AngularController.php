<?php

namespace Alex\BehatLauncher\Controller;

class AngularController extends Controller
{
    public function templateAction($name)
    {
        $templates = array(
            'project_list' => 'Project/list.html.twig',
            '_run_list'    => 'Run/_list.html.twig',
            'run_list'     => 'Run/list.html.twig',
        );

        if (!isset($templates[$name])) {
            throw $this->createNotFoundException(sprintf('No template named "%s".', $name));
        }

        $tpl = $this->getTwig()->loadTemplate($templates[$name]);

        return $tpl->renderBlock('angular', array());
    }
}
