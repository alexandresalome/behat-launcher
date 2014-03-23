<?php

namespace BehatLauncher\ServiceProvider;

use BehatLauncher\Console;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ConsoleProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['console'] = $app->share(function ($app) {
            return new Console($app['console_commands']);
        });

        $app['console_commands'] = $app->share(function ($app) {
            return $app['extensions']->getCommands($app);
        });
    }

    public function boot(Application $app)
    {
    }
}
