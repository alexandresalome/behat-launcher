<?php

namespace Alex\BehatLauncher\Extension\Core;

use Alex\BehatLauncher\Extension\AbstractExtension;

class CoreExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getControllers()
    {
        return array(
            'artifact'   => 'Alex\BehatLauncher\Extension\Core\Controller\ArtifactController',
            'project'    => 'Alex\BehatLauncher\Extension\Core\Controller\ProjectController',
            'run'        => 'Alex\BehatLauncher\Extension\Core\Controller\RunController',
            'frontend'   => 'Alex\BehatLauncher\Extension\Core\Controller\FrontendController',
        );
    }
}
