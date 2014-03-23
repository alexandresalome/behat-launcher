<?php

namespace BehatLauncher\Extensions\Core\Controller;

use BehatLauncher\Application;
use BehatLauncher\Extension\Controller\AbstractController;
use BehatLauncher\Model\Artifact;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;

class ArtifactController extends AbstractController
{
    public static function route(Application $app)
    {
        $id = self::id();
        $app->get('/output/{id}', $id.':showAction')->bind('artifact_show');
    }

    public function showAction($id)
    {
        $path = $this->getRunStorage()->getArtifactPath($id);
        $content = file_get_contents($path);

        return $content;
    }
}
