<?php

namespace BehatLauncher;

use BehatLauncher\Extension\ExtensionInterface;
use Silex\Application as BaseApplication;
use Silex\Provider as SilexProvider;

/**
 * Behat-Launcher application.
 */
class Application extends BaseApplication
{
    /**
     * Constructs the application
     */
    public function __construct()
    {
        parent::__construct(array(
            'debug'     => true,
            'root_dir'  => __DIR__.'/../..',
            'web_dir'   => __DIR__.'/../../web',
            'data_dir'  => __DIR__.'/../../data',
            'cache_dir' => __DIR__.'/../../data/cache',
        ));

        $this->registerProviders();

        $this->addExtension(new Extensions\Core\CoreExtension());
        $this->addExtension(new Extensions\Behat\BehatExtension());

        $this->get('/{any}', 'controller.frontend:showAction')->assert('any', '.*')->bind('fallback');
    }

    /**
     * Changes parameters for MySQL connection.
     */
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
        $this['extensions']->addExtension($extension);

        $extension->register($this);

        return $this;
    }

    private function registerProviders()
    {
        // vendor
        $this->register(new SilexProvider\UrlGeneratorServiceProvider());
        $this->register(new SilexProvider\TranslationServiceProvider(), array(
            'locale_fallback' => 'en'
        ));
        $this->register(new SilexProvider\FormServiceProvider());
        $this->register(new SilexProvider\ServiceControllerServiceProvider());
        $this->register(new SilexProvider\SerializerServiceProvider());
        $this->register(new SilexProvider\TwigServiceProvider(), array(
            'twig.path'    => $this->share(function ($app) {
                return $app['extensions']->getResourceWorkspace()->getDirectories('views');
            }),
            'debug'        => $this['debug'],
            'twig.options' => array(
                'cache' => $this['cache_dir'].'/twig',
                'strict_variables' => $this['debug'],
            ),
        ));

        // behat-launcher
        $this->register(new ServiceProvider\BehatLauncherProvider());
        $this->register(new ServiceProvider\ConsoleProvider());
        $this->register(new ServiceProvider\DoctrineProvider());
        $this->register(new ServiceProvider\AsseticProvider());
    }
}
