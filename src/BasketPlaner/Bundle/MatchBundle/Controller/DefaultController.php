<?php

namespace BasketPlaner\Bundle\MatchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BasketPlanerMatchBundle:Default:index.html.twig');
    }
}
