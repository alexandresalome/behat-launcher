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
