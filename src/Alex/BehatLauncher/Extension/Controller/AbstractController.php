<?php

namespace Alex\BehatLauncher\Extension\Controller;

use Alex\BehatLauncher\Application;
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

    protected function __construct(Application $app)
    {
        $this->app = $app;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function offsetGet($offset)
    {
        return $this->app[$offset];
    }

    /**
     * {@inheritdoc}
     */
    protected function offsetSet($offset, $value)
    {
        throw new \RuntimeException('Modification of application not allowed.');
    }

    /**
     * {@inheritdoc}
     */
    protected function offsetUnset($offset)
    {
        throw new \RuntimeException('Modification of application not allowed.');
    }

    /**
     * {@inheritdoc}
     */
    protected function offsetExists($offset)
    {
        return isset($this->app[$offset]);
    }

    protected function serialize($data, array $context = array())
    {
        $context['run_storage'] = $this->getRunStorage();
        $context['project_list'] = $this->getProjectList();

        $request = $this->application['request'];
        $accepted = explode(',', $request->headers->get('Accept'));
        foreach ($accepted as $accept) {
            if ($format = $request->getFormat(trim($accept))) {
                break;
            }
        }

        if ($format === 'html') {
            return $this->render('layout.html.twig');
        }

        if (!in_array($format, $expected = array('json', 'xml'))) {
            throw new BadRequestHttpException(sprintf('Cannot serialize to format "%s". Expected %s', $format, implode(' or ', $expected)));
        }

        return new Response($this->application['serializer']->serialize($data, $format, $context), 200, array(
            "Content-Type" => $request->getMimeType($format)
        ));
    }

    protected function unserialize($class)
    {
        $request = $this->application['request'];
        $accepted = explode(',', $request->headers->get('Content-Type'));
        foreach ($accepted as $accept) {
            if ($format = $request->getFormat(trim($accept))) {
                break;
            }
        }

        if (!in_array($format, $expected = array('json', 'xml'))) {
            throw new BadRequestHttpException(sprintf('Cannot deserialize from format "%s". Expected %s', $format, implode(' or ', $expected)));
        }

        return $this->application['serializer']->deserialize($request->getContent(), 'Alex\BehatLauncher\Model\Run', $format);
    }

    protected function render($template, array $parameters = array())
    {
        return $this->application['twig']->render($template, $parameters);
    }

    protected function redirect($url)
    {
        return new RedirectResponse($url);
    }

    protected function generateUrl($route, array $args = array())
    {
        return $this->application['url_generator']->generate($route, $args);
    }

    protected function createNotFoundException($message = 'Not Found')
    {
        return new NotFoundHttpException($message);
    }

    protected function jsonEncode($data)
    {
        return new JsonResponse($data);
    }
}
