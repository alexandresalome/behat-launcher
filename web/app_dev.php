<?php

require_once __DIR__.'/../vendor/autoload.php';

Symfony\Component\Debug\Debug::enable();

$app = new Alex\BehatLauncher\Application(array('debug' => true));
require_once __DIR__.'/../config.php';
$app->run();
