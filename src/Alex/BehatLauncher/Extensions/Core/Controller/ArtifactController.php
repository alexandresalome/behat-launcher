<?php

namespace Alex\BehatLauncher\Extension\Core\Controller;

use Alex\BehatLauncher\Application;
use Alex\BehatLauncher\Controller\AbstractController;
use Alex\BehatLauncher\Model\Artifact;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;

class ArtifactController extends AbstractController
{
    public static function route(Application $app)
    {
        $app->get('/output/{id}', 'controller.outputFile:showAction')->bind('outputFile_show');
    }

    public function showAction($id)
    {
        $path = $this->getRunStorage()->getArtifactPath($id);
        $content = file_get_contents($path);

        if (false !== strpos($content, '<html')) {
            $template = 'outputFile_html.html.twig';
        } else {
            $converter = new AnsiToHtmlConverter();
            $content = $converter->convert($content);
            $template = 'outputFile_text.html.twig';
        }

        return $this->render($template, array(
            'content' => $content
        ));
    }
}
