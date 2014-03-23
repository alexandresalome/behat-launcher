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
        return $this->jsonEncode($this['template_loader']->getSources());
    }

    public function langAction(Request $request)
    {
        $locale = $request->query->get('lang', 'en');
        if (!in_array($locale, array('en', 'fr'))) {
            $locale = 'en';
        }
        return $this->serialize(Yaml::parse(__DIR__.'/../Resources/locales/'.$locale.'.yml'));
    }
}
