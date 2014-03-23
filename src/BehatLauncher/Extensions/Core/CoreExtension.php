<?php

namespace BehatLauncher\Extensions\Core;

use BehatLauncher\Application;
use BehatLauncher\Extension\AbstractExtension;
use BehatLauncher\Extensions\Core\Frontend\AssetsManager;
use BehatLauncher\Extensions\Core\Frontend\TemplateLoader;
use BehatLauncher\Extensions\Core\Twig\DateExtension;

class CoreExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app['template_loader'] = $app->share(function ($app) {
            $loader = new TemplateLoader();
            $loader->addDirectories($app['extensions']->getAngularTemplatesDirs());

            return $loader;
        });

        $app->extend('twig', function ($twig, $app) {
            $twig->addExtension(new DateExtension($app['translator']));
            $twig->addExtension(new \Twig_Extension_StringLoader());

            return $twig;
        });

        $app['assets_manager'] = $app->share(function ($app) {
            $am = new AssetsManager();
            $am->addResourcesDirs($app['extensions']->getResourcesDirs());

            return $am;
        });
    }
}
