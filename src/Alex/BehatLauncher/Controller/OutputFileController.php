<?php

namespace Alex\BehatLauncher\Controller;

class OutputFileController extends Controller
{
    public function showAction($id)
    {
        $of = $this->getRunStorage()->getOutputFile($id);

        if ($of->isEmpty()) {
            throw new \RuntimeException('Output file does not exist or is empty.');
        }

        $content = $of->getContent();
        $mime = $of->getMimetype();

        if (false) {
        } else {
            $template = 'OutputFile/text.html.twig';
        }

        return $this->render($template, array(
            'content' => $content
        ));
    }
}
