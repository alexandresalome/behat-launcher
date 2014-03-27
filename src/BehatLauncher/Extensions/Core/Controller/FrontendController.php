<?php

namespace BehatLauncher\Extensions\Core\Controller;

use BehatLauncher\Application;
use BehatLauncher\Extension\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

class FrontendController extends AbstractController
{
    public static function route(Application $app)
    {
        $id = self::id();
        $app->get('/translations', $id.':langAction')->assert('any', '.*')->bind('lang');
        $app->get('/template', $id.':templateAction')->bind('template');
        $app->get('/', $id.':showAction')->bind('homepage');
    }

    public function showAction()
    {
        return $this->render('app.html.twig');
    }

    public function templateAction(Request $request)
    {
        $files = $this['extensions']->getResourceWorkspace()->getMergedFiles('/^templates\/.*\.html$/');
        $result = array();
        foreach ($files as $path => $file) {
            $result[substr($path, 10)] = file_get_contents($file);
        }

        return $this->jsonEncode($result);
    }

    public function langAction(Request $request)
    {
        $locale = $request->query->get('lang', 'en');
        if (!in_array($locale, array('en', 'fr'))) {
            $locale = 'en';
        }

        $data = array();
        $locale = preg_quote($locale);
        $files = $this['extensions']->getResourceWorkspace()->getMergedFiles('/^translations\/'.$locale.'\.yml$/');
        foreach ($files as $file) {
            $translations = Yaml::parse($file);
            $data = array_merge($data, $translations);
        }

        return $this->serialize($data);
    }
}
