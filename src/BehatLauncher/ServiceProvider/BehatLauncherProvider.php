<?php

namespace BehatLauncher\ServiceProvider;

use BehatLauncher\Extension\ExtensionManager;
use BehatLauncher\Workspace;
use Silex\Application;
use Silex\ServiceProviderInterface;

class BehatLauncherProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['workspace'] = $app->share(function ($app) {
            return new Workspace($app['em']);
        });

        $app['extensions'] = $app->share(function ($app) {
            return new ExtensionManager();
        });
    }

    public function boot(Application $app)
    {
    }
}
