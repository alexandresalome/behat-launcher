Behat Launcher
==============

An application to **launch your Behat tests from your browser**.

Installation
------------

Clone or download code, install dependencies and create a **config.php** file.

Initialize database:

.. code-block:: bash

    php behat-launcher init-db

Usage
-----

Launch background jobs:

.. code-block:: bash

    php behat-launcher run

Access ``http://localhost/`` and make tests.

Changelog
---------

**v0.1**

* First release

Roadmap
-------

**v0.1**

* Override Behat parameters
* Popin for output files
* Configure expected output formats

**Unplanned**

* Relaunch one unit
* Relaunch whole run
* Duplicate a run
* Automatic refresh of the page
