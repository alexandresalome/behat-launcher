<?php

namespace Alex\BehatLauncherBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RunController extends Controller
{
    public function listAction()
    {
        return $this->render('AlexBehatLauncherBundle:Run:list.html.twig', array(
            'runs' => $this->get('behat_launcher.run_storage')->getRuns()
        ));
    }

    public function createAction(Request $request, $project)
    {
        try {
            $project = $this->get('behat_launcher.project_list')->get($project);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException(sprintf('Project named "%s" not found.', $project));
        }

        $run = $project->createRun();

        $form = $this->createForm('behat_launcher_run', $run, array('project' => $project));

        if ($form->handleRequest($request)->isValid()) {
            $this->get('behat_launcher.run_storage')->saveRun($run);

            return $this->redirect($this->generateUrl('_behat_launcher_run_show', array('id' => $run->getId())));
        }

        return $this->render('AlexBehatLauncherBundle:Run:create.html.twig', array(
            'project' => $project,
            'form'    => $form->createView(),
        ));
    }

    public function showAction(Request $request, $id)
    {
        try {
            $run = $this->get('behat_launcher.run_storage')->getRun($id);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException(sprintf('Run #%s not found.', $id));
        }

        return $this->render('AlexBehatLauncherBundle:Run:show.html.twig', array(
            'run' => $run
        ));
    }
}
