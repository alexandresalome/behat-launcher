<?php

$app->configureMysql('localhost', 'behat_launcher', 'root', '');

$app->createProject('Bros', '/var/www/bros');

$app->createProject('Gitonomy', '/var/www/gitonomy')
    ->createProperty('Browser')
        ->setConfig('default.extensions.WebDriver\Behat\WebDriverExtension\Extension.browser')
        ->setFormType('choice')
        ->setFormOptions(array('choices' => array('firefox' => 'Mozilla Firefox', 'chrome' => 'Google Chrome')))
    ->endProperty()
;
