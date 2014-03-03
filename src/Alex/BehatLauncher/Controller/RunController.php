<?php

namespace Alex\BehatLauncher\Controller;

use Alex\BehatLauncher\Application;
use Symfony\Component\HttpFoundation\Request;

class RunController extends Controller
{
    public static function route(Application $app)
    {
        $app->get('/runs', 'controller.run:listAction')->bind('run_list');
        $app->post('/runs', 'controller.run:createAction')->bind('run_create');
        $app->get('/runs/{id}', 'controller.run:showAction')->bind('run_show');
        $app->post('/runs/{id}/restart', 'controller.run:restartAction')->bind('run_restart');
        $app->post('/runs/{id}/stop', 'controller.run:stopAction')->bind('run_stop');
        $app->post('/runs/{id}/delete', 'controller.run:deleteAction')->bind('run_delete');
    }

    public function createAction(Request $request)
    {
        $run = $this->unserialize('Alex\BehatLauncher\Behat\Run');

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
