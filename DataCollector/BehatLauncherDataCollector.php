<?php

namespace Alex\BehatLauncherBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

class BehatLauncherDataCollector implements DataCollectorInterface
{
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
    }

    public function getName()
    {
        return 'behat_launcher';
    }
}
