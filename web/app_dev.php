<?php

if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
    echo "Looks like dependencies are not installed. Please download composer (http://getcomposer.org) and install:\n";
    echo "$ curl http://getcomposer.org/installer | php\n";
    echo "$ php composer.phar install\n";
    die(1);
}

require_once __DIR__.'/../vendor/autoload.php';
Symfony\Component\Debug\Debug::enable();

$config = __DIR__.'/../config.php';
$app = new Alex\BehatLauncher\Application(array('debug' => true));
opcache_invalidate($config, true);
require_once $config;
$app->run();
