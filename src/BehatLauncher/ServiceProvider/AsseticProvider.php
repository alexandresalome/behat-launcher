<?php

namespace BehatLauncher\ServiceProvider;

use Assetic\AssetManager;
use Assetic\AssetWriter;
use Assetic\Asset\AssetCache;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Cache\FilesystemCache;
use Assetic\Extension\Twig\AsseticExtension;
use Assetic\Extension\Twig\TwigFormulaLoader;
use Assetic\Extension\Twig\TwigResource;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\LazyAssetManager;
use Assetic\FilterManager;
use Assetic\Filter\LessphpFilter;
use BehatLauncher\Extensions\Core\Frontend\AssetsManager;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Finder\Finder;

class AsseticProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['assetic.cache_dir'] = $app['cache_dir'].'/assetic';

        $app['assetic.asset_manager'] = $app->share(function ($app) {
            $am = new AssetManager();
            $am->set('js', $app['assetic.asset_collection.js']);
            $am->set('css', $app['assetic.asset_collection.css']);

            return $am;
        });

        $app['assetic.writer'] = $app->share(function ($app) {
            $am = $app['assetic.lazy_asset_manager'];

            // enable loading assets from twig templates
            $finder = Finder::create()->in($app['extensions']->getTwigTemplatesDirs())->files();
            foreach ($finder as $file) {
                $resource = new TwigResource($app['twig']->getLoader(), $file->getRelativePathname());
                $am->addResource($resource, 'twig');
            }

            $writer = new AssetWriter($app['web_dir']);

            return $writer;
        });

        $app['assetic.lazy_asset_manager'] = $app->share(function ($app) {
            $lam = new LazyAssetManager($app['assetic.asset_factory']);

            $lam->setLoader('twig', new TwigFormulaLoader($app['twig']));

            return $lam;

        });

        $app['assetic.filter_manager'] = $app->share(function ($app) {
            $fm = new FilterManager();
            $fm->set('lessphp', new LessphpFilter());

            return $fm;
        });

        $app['assetic.asset_factory'] = $app->share(function ($app) {
            $af = new AssetFactory($app['web_dir'], $app['debug']);
            $af->setFilterManager($app['assetic.filter_manager']);
            $af->setAssetManager($app['assetic.asset_manager']);
            $af->setDebug(false);

            return $af;
        });

        $app['assetic.cache'] = $app->share(function ($app) {
            return new FilesystemCache($app['assetic.cache_dir']);
        });

        $app['assetic.asset_collection.js'] = $app->share(function ($app) {
            $bower = $app['root_dir'].'/bower_components/';

            $defaultFiles = array(
                $bower.'/jquery/dist/jquery.js',
                $bower.'/bootstrap/dist/js/bootstrap.js',
                $bower.'/angular/angular.js',
                $bower.'/angular-sanitize/angular-sanitize.js',
                $bower.'/angular-translate/angular-translate.js',
                $bower.'/angular-translate-loader-url/angular-translate-loader-url.js',
                $bower.'/angular-translate-interpolation-messageformat/angular-translate-interpolation-messageformat.js',
                $bower.'/angular-route/angular-route.js',
                $bower.'/angular-resource/angular-resource.js',
                $bower.'/angular-local-storage/angular-local-storage.js',
                $bower.'/messageformat/messageformat.js',
                $bower.'/messageformat/locale/fr.js',
                $bower.'/messageformat/locale/en.js',
                $app['root_dir'].'/src/BehatLauncher/Resources/js/app.js',
            );

            $coll = new AssetCollection();
            foreach (array_merge($defaultFiles, $app['extensions']->getJavascriptFiles()) as $file) {
                $coll->add(new AssetCache(new FileAsset($file), $app['assetic.cache']));
            }

            return $coll;
        });

        $app['assetic.asset_collection.css'] = $app->share(function ($app) {

            $bower = $app['root_dir'].'/bower_components/';
            $defaultFiles = array($bower.'bootstrap/dist/css/bootstrap.css');

            $coll = new AssetCollection();
            foreach (array_merge($defaultFiles, $app['extensions']->getStylesheetFiles()) as $file) {
                $coll->add(new AssetCache(new FileAsset($file), $app['assetic.cache']));
            }

            return $coll;
        });

        // extend twig
        $app['twig'] = $app->share($app->extend('twig', function ($twig, $app) {
            $twig->addExtension(new AsseticExtension($app['assetic.asset_factory']));

            return $twig;
        }));
    }

    public function boot(Application $app)
    {
    }
}
