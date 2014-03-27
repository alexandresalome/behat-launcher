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
        parent::register($app);

        $app['twig'] = $app->share($app->extend('twig', function ($twig, $app) {
            $twig->addExtension(new DateExtension($app['translator']));
            $twig->addExtension(new \Twig_Extension_StringLoader());

            return $twig;
        }));
    }
}
