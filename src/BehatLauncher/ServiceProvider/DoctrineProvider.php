<?php

namespace BehatLauncher\ServiceProvider;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Silex\Application;
use Silex\ServiceProviderInterface;

class DoctrineProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['connection'] = $app->share(function ($app) {
            return DriverManager::getConnection(array(
                'driver'   => 'pdo_mysql',
                'host'     => $app['db_host'],
                'dbname'   => $app['db_name'],
                'user'     => $app['db_user'],
                'password' => $app['db_password'],
            ));
        });

        $app['em'] = $app->share(function ($app) {
            $config = Setup::createAnnotationMetadataConfiguration($app['extensions']->getModelPaths(), true, null, null, false);

            return EntityManager::create($app['connection'], $config);
        });
    }

    public function boot(Application $app)
    {
    }
}
