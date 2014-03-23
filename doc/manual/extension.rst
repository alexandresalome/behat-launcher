Creating extension
==================

To create a new extension, first, create the extension class:

.. code-block:: php

    # src/Acme/BehatLauncher/Extensions/Demo/DemoExtension.php

    namespace Acme\BehatLauncher\Extensions\Demo;

    use Alex\BehatLauncher\Extension\AbstractExtension;

    class DemoExtension extends AbstractExtension
    {
    }

Then, register it in the application:

.. code-block:: php

    # config.php

    use Acme\BehatLauncher\Extensions\Demo\DemoExtension;

    $app->addExtension(new DemoExtension());

Access web application for futher installation.

Register routes
---------------

A Behat-Launcher extension can register routes to the application. First, in
your extension class, declare controller:

.. code-block:: php

    # src/Acme/BehatLauncher/Extensions/Demo/Controller/DemoExtension.php

    # ...

    class DemoExtension extends AbstractExtension
    {
        public function getControllers()
        {
            return array(
                'test' => 'Acme\BehatLauncher\Extensions\Demo\Controller\TestController',
            );
        }
    }

Then, create a controller class in *Controller/* folder:

.. code-block:: php

    # src/Acme/BehatLauncher/Extensions/Demo/Controller/TestController.php

    use Alex\BehatLauncher\Extension\Controller\AbstractController;

    class TestController extends AbstractController
    {
        public static function route(Application $app)
        {
            $app->get('/test', 'controller.test:indexAction')->bind('test_index');
        }

        public function indexAction()
        {
            return $this->render('some/template.html.twig');
        }
    }

Register model entities
-----------------------

You can create new entity to persist. Just create a folder *Model/* containing Doctrine entities and they will automatically be persisted to database.
