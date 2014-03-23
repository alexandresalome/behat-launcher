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

Create new routes
-----------------

An extension can register routes to the application:

.. code-block:: php

    # src/Acme/BehatLauncher/Extensions/Demo/Controller/TestController.php

    use Alex\BehatLauncher\Extension\Controller\AbstractController;

    class TestController extends AbstractController
    {
        public static function route(Application $app)
        {
            $id = self::id();
            $app->get('/test', $id.':indexAction')->bind('test_index');
        }

        public function indexAction()
        {
            return $this->render('some/template.html.twig');
        }
    }

Add your views to twig
----------------------

Create a folder named *Resources/views* and they'll be automatically available to Twig.

Add your templates to angular
-----------------------------

Create a folder named *Resources/templates* and they'll be automatically available to Angular.

Register model entities
-----------------------

You can create new entity to persist. Just create a folder *Model/* containing Doctrine entities and they will automatically be persisted to database.
