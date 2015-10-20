<?php

namespace BasketPlaner\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BasketPlanerMainBundle:Default:index.html.twig');
    }
}
