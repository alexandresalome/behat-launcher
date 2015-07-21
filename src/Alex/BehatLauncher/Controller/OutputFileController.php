<?php

namespace Alex\BehatLauncher\Controller;

use Alex\BehatLauncher\Application;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;

class OutputFileController extends Controller
{
    public static function route(Application $app)
    {
        $app->get('/output/{id}', 'controller.outputFile:showAction')->bind('outputFile_show');
    }

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

            // extract screenshots messages
            preg_match_all('#Screenshot at: (.*)#', $content, $screenshots, PREG_OFFSET_CAPTURE);
            foreach ($screenshots[0] as $screenshot)
            {
                // extract screenshot path
                $screenshotPath = str_replace("Screenshot at: ", "", $screenshot[0]);
                $image = $this->generateImage(trim($screenshotPath));

                // add <img> tag
                $content = str_replace("Screenshot at: " . $screenshotPath, '<a href="file:///' . $screenshotPath . '">' . $screenshotPath . '</a>'
                    . '<p><img src="data:image/png;base64,' . $image . ' "/>', $content);

                $image = null;
                $screenshotPath = null;
            }
        }

        return $this->render($template, array(
            'content' => $content
        ));
    }

    /**
     * Generate raw image from existing png
     *
     * @param $pngFilePath
     * @return string
     */
    public function generateImage($pngFilePath)
    {
        if (file_exists($pngFilePath))
        {
            $image = imagecreatefrompng($pngFilePath);

            // start buffering
            ob_start();
            imagepng($image);
            $contents = ob_get_contents();
            ob_end_clean();
            imagedestroy($image);

            return base64_encode($contents);
        }
        else
        {
            return '';
        }
    }
}
