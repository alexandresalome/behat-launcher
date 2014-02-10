AlexBehatLauncherBundle
=======================

**NOT WORKING** *Github is my backup*

A bundle to launch Behat tests from your browser.

Features
--------

* Launch tests and get reports on a web interface
* Override Behat configuration

Installation
------------

In your **composer.json**:

.. code-block:: json

    {
        "require": {
            "alexandresalome/behat-launcher-bundle": "~0.1"
        }
    }

Add to your **AppKernel.php**:

.. code-block:: php

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            return array(
                new Alex\BehatLauncherBundle\AlexBehatLauncherBundle()
            );
        }
    }

In your **config.yml**:


.. code-block:: yaml

    alex_behat_launcher:
        projects:
            -
                name: My project
                path: ../../behat.yml # path is relative from config file to behat config
                properties:
                    -
                        name: "Browser"
                        config: behat.extensions.WebDriver\Behat\WebDriverExtension\Extension.browser
                        form_type: choice
                        form_options
                            choices: [ "firefox": "Mozilla Firefox", "chrome": "Google Chrome" ]
                    -
                        name: "Extra"
                        env: FOOBAR
                        form_type: text
                        form_options: { "required": false }
                output:
                    - { format: failed }
                    - { format: pretty }
                    - { format: journal }
                    - { format: html }

Usage
-----

Launch background jobs:

.. code-block:: bash

    php app/console behat-launcher:run

Access ``http://localhost/app_dev.php/_behat`` and make tests.
