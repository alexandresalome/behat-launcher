<?php

namespace Alex\BehatLauncher\Controller;

use Alex\BehatLauncher\Application;

class FrontendController extends Controller
{
    public static function route(Application $app)
    {
        $app->get('/{any}', 'controller.frontend:showAction')->assert('any', '.*')->bind('homepage');
    }
    public function showAction()
    {
        return $this->render('layout.html.twig');
    }
}
