<?php

namespace Alex\BehatLauncherBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OutputFileController extends Controller
{
    public function showAction($id)
    {
        $of = $this->get('behat_launcher.run_storage')->getOutputFile($id);
        if ($of->isEmpty()) {
            throw new \RuntimeException('Output file does not exist or is empty.');
        }

        $content = $of->getContent();
        $mime = $of->getMimetype();

        if (false) {
        } else {
            $template = 'AlexBehatLauncherBundle:OutputFile:text.html.twig';
        }

        return $this->render($template, array(
            'content' => $content
        ));
    }
}
