<?php

namespace Alex\BehatLauncher\Controller;

class FrontendController extends Controller
{
    public function showAction()
    {
        return $this->render('layout.html.twig');
    }
}
