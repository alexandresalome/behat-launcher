#!/bin/bash
set -e
cd `php -r "echo dirname(realpath('$0'));"`

if [ ! -f composer.phar ]; then
    curl http://getcomposer.org/installer | php
fi

if [ ! -d vendor ]; then
    php composer.phar install
fi

php behat-launcher init-db

php bin/behat
php bin/phpunit
