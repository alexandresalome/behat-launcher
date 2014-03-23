<?php

namespace BehatLauncher\Extensions\Core\Controller;

use BehatLauncher\Application;
use BehatLauncher\Extension\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class RunController extends AbstractController
{
    public static function route(Application $app)
    {
        $id = self::id();
        $app->get('/runs', $id.':listAction')->bind('run_list');
        $app->post('/runs', $id.':createAction')->bind('run_create');
        $app->get('/runs/{id}', $id.':showAction')->bind('run_show');
        $app->post('/runs/{id}/restart', $id.':restartAction')->bind('run_restart');
        $app->post('/runs/{id}/stop', $id.':stopAction')->bind('run_stop');
        $app->post('/runs/{id}/delete', $id.':deleteAction')->bind('run_delete');
    }

    public function createAction(Request $request)
    {
        $run = $this->unserialize('BehatLauncher\Model\Run');

        if (!$this->getProjectList()->get($run->getProjectName())) {
            throw $this->createNotFoundException(sprintf('Project named "%s" not found.', $project));
        }

        $this->getRunStorage()->saveRun($run);

        return $this->redirect($this->generateUrl('run_show', array('id' => $run->getId())));
    }

    public function listAction(Request $request)
    {
        return $this->serialize($this->getRunStorage()->getRuns());
    }

    public function showAction(Request $request, $id)
    {
        try {
            $run = $this->getRunStorage()->getRun($id);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException(sprintf('Run #%s not found.', $id));
        }

        return $this->serialize($run, array('run_details' => true));
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
