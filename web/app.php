<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Alex\BehatLauncher\Application();
require_once __DIR__.'/../config.php';
$app->run();
