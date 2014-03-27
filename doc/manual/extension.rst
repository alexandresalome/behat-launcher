Extending Behat-Launcher
========================

Behat-Launcher aims to be flexible and adaptable to any system, application, or context. Creating extensions for Behat-Launcher should always be easy and smart.

Create a new extension
~~~~~~~~~~~~~~~~~~~~~~

Extensions allow you to add your own logic to Behat-Launcher. Creating an extension is as easy as creating a class:

.. code-block:: php

    # src/BehatLauncher/Extensions/Demo/DemoExtension.php

    namespace BehatLauncher\Extensions\Demo;

    use Alex\BehatLauncher\Extension\AbstractExtension;

    class DemoExtension extends AbstractExtension
    {
    }

Then, register it in the application:

.. code-block:: php

    # config.php

    use BehatLauncher\Extensions\Demo\DemoExtension;

    $app->addExtension(new DemoExtension());

That's it! ! Easy, isn't it? Now let's see how to create new templates, new controllers...

Extension features
~~~~~~~~~~~~~~~~~~

New controllers and routes
--------------------------

In **Behat-Launcher**, routes are declared by extensions. Each extension can register how many routes they
want, by doing so:

.. code-block:: php

    # src/BehatLauncher/Extensions/Demo/Controller/TestController.php

    use BehatLauncher\Extension\Controller\AbstractController;

    class TestController extends AbstractController
    {
        public static function route(Application $app)
        {
            $id = self::id();
            $app->get('/hello', $id.':indexAction')->bind('test_hello');
        }

        public function helloAction()
        {
            return 'Hello world!';
        }
    }

The freshly created route is named *test_hello* and will return a text "Hello world!". You can access it through */hello*.

This controller will be placed in a *Controller/* folder of the extension. All controllers located in *Controller/* folder of an extension is automatically registered to the application.

New views for twig
------------------

Twig views are also provided by extensions. Located under *Resources/views*, those views are automatically published to the Twig engine.

If your template is in "*src/BehatLauncher/Extensions/Demo/Resources/views/user/show.html*", it will be available under name "*views/user/show.html*".

So, in your extension, you can have templates like this:

.. code-block:: text

    Extensions/
        Demo/
            Resources/
                views/
                    user/
                        info.html
                    custom.html

You can use them in templates like this::

.. code-block:: html+jinja

    {% extends "custom.html" %}

    {% block content %}
        {{ include("user/info.html") }}
    {% endblock %}

New templates for angular
-------------------------

Same philosophy here: create your files in *Resources/templates/* folder, and they'll automatically be available to Angular.

For example, if you create file *Resources/templates/my-block.html*, you can do with angular:

.. code-block:: html

    <div ng-include="'my-block.html"></div>

New model entities
------------------

In your extension, you can create custom entities that will be persisted through Doctrine.

Create your Doctrine classes in a directory *Model/*. Annotate them with Doctrine annotations and they'll automatically be persisted.

For example:

.. code-block:: php

    # src/BehatLauncher/Extensions/Demo/Model/Pizza.php

    namespace BehatLauncher\Extensions\Demo\Model;

    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity
     */
    class Pizza
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue
         */
        private $id;

        /**
         * @ORM\Column(type="string", length=128)
         */
        private $name;
    }

New Javascript files
--------------------

If you have Javascript you want to integrate on frontend, just create your Javascript files in *Resources/js*.

Those files will automatically be integrated in the Javascript file, for frontend:

.. code-block:: javascript

    // src/BehatLauncher/Extensions/Demo/Resources/js/alert.js
    alert("This is a noisy message to show you can inject Javascript in pages.");

New stylesheets files
---------------------

If you have LESS stylesheets you want to integrate on frontend, just create your LESS files in *Resources/less*.

Those files will automatically be integrated in the CSS file, for frontend:

.. code-block:: less

    // src/BehatLauncher/Extensions/Demo/Resources/less/test.less
    * {
        color: red;
    }

New translation files
---------------------

To provide new translation sentences, create a file *{locale}.yml* in *Resources/translations* folder. They will automatically be merged with other translations.

For example, the Yaml file:

.. code-block:: yaml

    # src/BehatLauncher/Extensions/Demo/Resources/translations/en.yml
    demo.test: "This is a test translation"

And to use it, in your templates:

.. code-block:: html

    <div>
        {{ 'demo.test'|translate }}
    </div>
