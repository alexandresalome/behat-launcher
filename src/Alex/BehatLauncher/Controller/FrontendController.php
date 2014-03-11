<?php

namespace Alex\BehatLauncher\Controller;

use Alex\BehatLauncher\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

class FrontendController extends Controller
{
    public static function route(Application $app)
    {
        $app->get('/translations', 'controller.frontend:langAction')->assert('any', '.*')->bind('lang');
        $app->get('/template', 'controller.frontend:templateAction')->bind('template');
        $app->get('/{any}', 'controller.frontend:showAction')->assert('any', '.*')->bind('homepage');
    }

    public function showAction()
    {
        return $this->render('layout.html.twig');
    }

    public function templateAction(Request $request)
    {
        $loader = $this->getTemplateLoader();

        return $this->jsonEncode($loader->getSources());
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
