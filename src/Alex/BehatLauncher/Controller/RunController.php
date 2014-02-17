<?php

namespace Alex\BehatLauncher\Controller;

use Symfony\Component\HttpFoundation\Request;

class RunController extends Controller
{
    public function listAction()
    {
        $result = array();

        foreach ($this->getRunStorage()->getRuns() as $run) {
            $result[] = $run->toArray();
        }

        return json_encode($result);
    }

    public function createAction(Request $request, $project)
    {
        try {
            $project = $this->getProjectList()->get($project);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException(sprintf('Project named "%s" not found.', $project));
        }

        $run = $project->createRun();

        $form = $this->createForm('behat_launcher_run', $run, array('project' => $project));

        if ($form->handleRequest($request)->isValid()) {
            $this->getRunStorage()->saveRun($run);

            return $this->redirect($this->generateUrl('run_show', array('id' => $run->getId())));
        }

        return $this->render('Run/create.html.twig', array(
            'project' => $project,
            'form'    => $form->createView(),
        ));
    }

    public function showAction($id)
    {
        try {
            $run = $this->getRunStorage()->getRun($id);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException(sprintf('Run #%s not found.', $id));
        }

        return json_encode($run->toArray(true));
    }

    public function restartAction(Request $request, $id)
    {
        $unitQuery = $request->query->get('unit');

        try {
            $run = $this->getRunStorage()->getRun($id);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException(sprintf('Run #%s not found.', $id));
        }

        $failed = $request->query->get('failed');

        foreach ($run->getUnits() as $unit) {
            if ($unitQuery && $unitQuery != $unit->getId()) {
                continue;
            }

            if ($failed && !$unit->isFailed()) {
                continue;
            }

            if ($unit->isFinished()) {
                $unit->reset();
                $this->getRunStorage()->saveRunUnit($unit);
            }
        }

        return $this->redirect($this->generateUrl('run_show', array('id' => $id)));
    }

    public function stopAction(Request $request, $id)
    {
        try {
            $run = $this->getRunStorage()->getRun($id);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException(sprintf('Run #%s not found.', $id));
        }

        foreach ($run->getUnits() as $unit) {
            if ($unit->isPending()) {
                $unit->stop();
                $this->getRunStorage()->saveRunUnit($unit);
            }
        }

        return $this->redirect($this->generateUrl('run_show', array('id' => $id)));
    }

    public function deleteAction(Request $request, $id)
    {
        try {
            $run = $this->getRunStorage()->getRun($id);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException(sprintf('Run #%s not found.', $id));
        }

        $this->getRunStorage()->deleteRun($run);

        return $this->redirect($this->generateUrl('run_list'));
    }
}
