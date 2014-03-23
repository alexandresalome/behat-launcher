<?php

namespace BehatLauncher\Extensions\Core\Controller;

use BehatLauncher\Application;
use BehatLauncher\Extension\Controller\AbstractController;

class AssetController extends AbstractController
{
    public static function route(Application $app)
    {
        $id = self::id();
        $app->get('/css/all.css', $id.':cssAction')->bind('asset_css');
        $app->get('/js/all.js', $id.':jsAction')->bind('asset_js');
    }

    public function cssAction()
    {
        $am = $this['assets_manager'];

        return $am->getCssContent();
    }

    public function jsAction()
    {
        $am = $this['assets_manager'];

        return $am->getJsContent();
    }
}
