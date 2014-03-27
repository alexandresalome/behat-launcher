<?php

namespace BehatLauncher\Extension\Controller;

use BehatLauncher\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Base controller for Behat-Launcher.
 */
abstract class AbstractController implements \ArrayAccess
{
    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public static function route(Application $app)
    {
        throw new \RuntimeException(sprintf('%s::route() not implemented.', self::class));
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->app[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        throw new \RuntimeException('Modification of application not allowed.');
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        throw new \RuntimeException('Modification of application not allowed.');
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->app[$offset]);
    }

    protected function serialize($data, array $context = array())
    {
        $context['app'] = $this->app;

        $request = $this->app['request'];
        $accepted = explode(',', $request->headers->get('Accept'));
        foreach ($accepted as $accept) {
            if ($format = $request->getFormat(trim($accept))) {
                break;
            }
        }

        if ($format === 'html') {
            return $this->render('app.html.twig');
        }

        if (!in_array($format, $expected = array('json', 'xml'))) {
            throw new BadRequestHttpException(sprintf('Cannot serialize to format "%s". Expected %s', $format, implode(' or ', $expected)));
        }

        return new Response($this->app['serializer']->serialize($data, $format, $context), 200, array(
            "Content-Type" => $request->getMimeType($format)
        ));
    }

    protected function unserialize($class)
    {
        $request = $this->app['request'];
        $accepted = explode(',', $request->headers->get('Content-Type'));
        foreach ($accepted as $accept) {
            if ($format = $request->getFormat(trim($accept))) {
                break;
            }
        }

        if (!in_array($format, $expected = array('json', 'xml'))) {
            throw new BadRequestHttpException(sprintf('Cannot deserialize from format "%s". Expected %s', $format, implode(' or ', $expected)));
        }

        return $this->app['serializer']->deserialize($request->getContent(), 'BehatLauncher\Model\Run', $format);
    }

    protected function render($template, array $parameters = array())
    {
        return $this->app['twig']->render($template, $parameters);
    }

    protected function redirect($url)
    {
        return new RedirectResponse($url);
    }

    protected function generateUrl($route, array $args = array())
    {
        return $this->app['url_generator']->generate($route, $args);
    }

    protected function createNotFoundException($message = 'Not Found')
    {
        return new NotFoundHttpException($message);
    }

    protected function jsonEncode($data)
    {
        return new JsonResponse($data);
    }

    public static function id()
    {
        $class = static::class;
        $class = substr($class, strrpos($class, '\\') + 1);
        $class = strtolower(substr($class, 0, -10));

        return 'controller.'.$class;
    }
}
