<?php

umask(0000);

// Verify composer installation
if (!file_exists(__DIR__.'/vendor/autoload.php')) {
    echo "Looks like dependencies are not installed. Please download composer (http://getcomposer.org) and install:\n";
    echo "$ curl http://getcomposer.org/installer | php\n";
    echo "$ php composer.phar install\n";
    die(1);
}

// Verify configuration
if (!file_exists(__DIR__.'/config.php')) {
    echo "Looks like application is not configured. Create config.php file\n";
    echo "$ cp config.php.dist config.php\n";
    die(1);
}

// composer autoloading
$loader = require_once __DIR__.'/vendor/autoload.php';
Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

// enable debug
Symfony\Component\Debug\Debug::enable();

$app = new BehatLauncher\Application();
require_once __DIR__.'/config.php';

return $app;
