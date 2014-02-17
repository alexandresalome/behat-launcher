<?php

namespace Alex\BehatLauncher\Controller;

use Alex\BehatLauncher\Behat\OutputFile;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;

class OutputFileController extends Controller
{
    public function showAction($id)
    {
        $path = $this->getRunStorage()->getOutputFilePath($id);
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
