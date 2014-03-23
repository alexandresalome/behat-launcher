<?php

namespace Alex\BehatLauncher;

use Alex\BehatLauncher\Extension\ExtensionInterface;
use Alex\BehatLauncher\Extension\ExtensionManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Silex\Application as BaseApplication;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SerializerServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;

class Application extends BaseApplication
{
    public function __construct(array $values = array())
    {
        parent::__construct($values);

        $this->registerProviders();
        $this->registerServices();
        $this->registerControllers();
    }

    public function configureMysql($host, $database, $user, $password)
    {
        $this['db_host']     = $host;
        $this['db_name']     = $database;
        $this['db_user']     = $user;
        $this['db_password'] = $password;

        return $this;
    }

    /**
     * Adds an extension to Behat-Launcher application.
     *
     * @param ExtensionInterface $extension
     *
     * @return Application
     */
    public function addExtension(ExtensionInterface $extension)
    {
        $this['extensions']->add($extension);

        return $this;
    }

    private function registerProviders()
    {
        $this->register(new UrlGeneratorServiceProvider());
        $this->register(new TranslationServiceProvider(), array('locale_fallback' => 'en'));
        $this->register(new FormServiceProvider());
        $this->register(new ServiceControllerServiceProvider());
        $this->register(new TwigServiceProvider(), array(
            'twig.path'    => __DIR__.'/Resources/views',
            'debug'        => $this['debug'],
            'twig.options' => array('cache' => __DIR__.'/../../../data/cache/twig'),
        ));
        $this->register(new SerializerServiceProvider());
    }

    private function registerServices()
    {
        $this['connection'] = $this->share(function ($app) {
            return DriverManager::getConnection(array(
                'driver'   => 'pdo_mysql',
                'host'     => $app['db_host'],
                'dbname'   => $app['db_name'],
                'user'     => $app['db_user'],
                'password' => $app['db_password'],
            ));
        });

        $this['em'] = $this->share(function ($app) {
            $config = Setup::createAnnotationMetadataConfiguration($app['extensions']->getModelPaths(), true, null, null, false);

            return EntityManager::create($app['connection'], $config);
        });

        $this['workspace'] = $this->share(function ($app) {
            return new Workspace($app['project_list'], $app['run_storage']);
        });

        $this['template_loader'] = $this->share(function ($app) {
            $loader = new TemplateLoader();
            $loader->addDirectory(__DIR__.'/../../../assets/templates');

            return $loader;
        });

        $this->extend('twig', function ($twig, $app) {
            $twig->addExtension(new DateExtension($app['translator']));
            $twig->addExtension(new \Twig_Extension_StringLoader());

            return $twig;
        });

        $this['extensions'] = $this->share(function ($app) {
            $em = new ExtensionManager();
            $em->addExtension(new Extension\Core\CoreExtension());
            $em->addExtension(new Extension\Behat\BehatExtension());

            return $em;
        });
    }

    private function registerControllers()
    {
        // Controllers as service
        foreach ($this['extensions']->getControllers() as $id => $class) {
            $this['controller.'.$id] = $this->share(function($app) use ($class) {
                return new $class($app);
            });

            call_user_func($class.'::route', $this);
        }

    }
}
