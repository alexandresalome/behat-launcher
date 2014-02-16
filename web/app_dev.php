<?php

require_once __DIR__.'/../vendor/autoload.php';
Symfony\Component\Debug\Debug::enable();

$config = __DIR__.'/../config.php';
$app = new Alex\BehatLauncher\Application(array('debug' => true));
opcache_invalidate($config, true);
require_once $config;
$app->run();
