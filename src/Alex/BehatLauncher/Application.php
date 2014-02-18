<?php

namespace Alex\BehatLauncher;

use Alex\BehatLauncher\Behat\MysqlStorage;
use Alex\BehatLauncher\Behat\Project;
use Alex\BehatLauncher\Behat\ProjectList;
use Alex\BehatLauncher\Form\BehatLauncherExtension;
use Alex\BehatLauncher\Twig\DateExtension;
use Doctrine\DBAL\DriverManager;
use Silex\Application as BaseApplication;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;

class Application extends BaseApplication
{
    const VERSION = 'dev-master';

    public function __construct(array $values = array())
    {
        parent::__construct($values);

        $this->registerProviders();
        $this->registerServices();
        $this->registerRouting();
    }

    public function configureMysql($host, $database, $user, $password)
    {
        $this['db_host']     = $host;
        $this['db_name']     = $database;
        $this['db_user']     = $user;
        $this['db_password'] = $password;

        return $this;
    }

    public function createProject($name, $path)
    {
        $project = new Project();
        $project
            ->setName($name)
            ->setPath($path)
        ;

        $this['project_list']->add($project);

        return $project;
    }

    /**
     * Returns the workspace.
     *
     * @return Workspace
     */
    public function getWorkspace()
    {
        return $this['workspace'];
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
    }

    private function registerServices()
    {
        $this['db'] = $this->share(function ($app) {
            return DriverManager::getConnection(array(
                'driver'   => 'pdo_mysql',
                'host'     => $app['db_host'],
                'dbname'   => $app['db_name'],
                'user'     => $app['db_user'],
                'password' => $app['db_password'],
            ));
        });

        $this['project_list'] = $this->share(function () {
            return new ProjectList();
        });

        $this['run_storage'] = $this->share(function ($app) {
            return new MysqlStorage($app['db'], __DIR__.'/../../../data/output_files');
        });

        $this['workspace'] = $this->share(function ($app) {
            return new Workspace($app['project_list'], $app['run_storage']);
        });

        if ($this['debug']) {
            $this->register($profiler = new WebProfilerServiceProvider(), array(
                'profiler.cache_dir' => __DIR__.'/../../../data/cache/profiler',
            ));
            $this->mount('/_profiler', $profiler);
        }

        $this->extend('twig', function ($twig, $app) {
            $twig->addExtension(new DateExtension($app['translator']));
            $twig->addExtension(new \Twig_Extension_StringLoader());

            return $twig;
        });

        $controllers = array(
            'outputFile' => 'Alex\BehatLauncher\Controller\OutputFileController',
            'project'    => 'Alex\BehatLauncher\Controller\ProjectController',
            'run'        => 'Alex\BehatLauncher\Controller\RunController',
            'angular'    => 'Alex\BehatLauncher\Controller\AngularController',
            'frontend'   => 'Alex\BehatLauncher\Controller\FrontendController',
        );

        // Controllers as service
        foreach ($controllers as $id => $class) {
            $this['controller.'.$id] = $this->share(function($app) use ($class) {
                return new $class($app);
            });
        }
    }

    private function registerRouting()
    {
        // API
        $this->get('/project_list.json', 'controller.project:listAction')->bind('project_list');
        $this->get('/run_list.json', 'controller.run:listAction')->bind('run_list');
        $this->get('/runs/{id}.json', 'controller.run:showAction')->bind('run_show');
        $this->get('/runs/{id}/restart', 'controller.run:restartAction')->bind('run_restart');
        $this->get('/output/{id}', 'controller.outputFile:showAction')->bind('outputFile_show');

        // Front page
        $this->get('/{foo}', 'controller.frontend:showAction')->assert('foo', '.*')->bind('homepage');

        $this->extend('form.extensions', function ($extensions, $app) {
            $extensions[] = new BehatLauncherExtension();

            return $extensions;
        });
    }
}
