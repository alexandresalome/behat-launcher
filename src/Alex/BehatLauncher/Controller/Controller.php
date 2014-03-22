<?php

namespace Alex\BehatLauncher\Controller;

use Alex\BehatLauncher\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Base controller for Behat Launcher.
 */
abstract class Controller
{
    /**
     * @var Application
     */
    private $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function getProjectList()
    {
        return $this->application['project_list'];
    }

    public function getRunStorage()
    {
        return $this->application['run_storage'];
    }

    public function getTemplateLoader()
    {
        return $this->application['template_loader'];
    }

    public function serialize($data, array $context = array())
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

    public function unserialize($class)
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

    public function render($template, array $parameters = array())
    {
        return $this->application['twig']->render($template, $parameters);
    }

    public function renderWithAngular(Request $request, $template, array $parameters = array())
    {
        if ($request->query->get('_angular')) {
            $template = $this->application['twig']->loadTemplate($template);
            if ($template->hasBlock('angular')) {
                return $template->renderBlock('angular', $parameters);
            } elseif ($template->hasBlock('content')) {
                return $template->renderBlock('content', $parameters);
            }
        }

        return $this->render($template, $parameters);
    }

    public function redirect($url)
    {
        return new RedirectResponse($url);
    }

    public function generateUrl($route, array $args = array())
    {
        return $this->application['url_generator']->generate($route, $args);
    }

    public function createForm($name, $data = null, array $options = array())
    {
        return $this->application['form.factory']->create($name, $data, $options);
    }

    public function createNotFoundException($message = 'Not Found')
    {
        return new NotFoundHttpException($message);
    }

    public function jsonEncode($data)
    {
        return new JsonResponse($data);
    }
}
