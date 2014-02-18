<?php

namespace Alex\BehatLauncher\Controller;

use SensioLabs\AnsiConverter\AnsiToHtmlConverter;

class OutputFileController extends Controller
{
    public function showAction($id)
    {
        $of = $this->getRunStorage()->getOutputFile($id);

        if ($of->isEmpty()) {
            throw new \RuntimeException('Output file does not exist or is empty.');
        }

        $content = $of->getContent();

        if (false !== strpos($content, '<html')) {
            $template = 'OutputFile/html.html.twig';
        } else {
            $converter = new AnsiToHtmlConverter();
            $content = $converter->convert($content);
            $template = 'OutputFile/text.html.twig';
        }

        return $this->render($template, array(
            'content' => $content
        ));
    }
}
