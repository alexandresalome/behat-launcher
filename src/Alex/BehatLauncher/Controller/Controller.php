<?php

namespace Alex\BehatLauncher\Controller;

use Alex\BehatLauncher\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
}
