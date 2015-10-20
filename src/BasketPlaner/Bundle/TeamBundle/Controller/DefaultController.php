<?php

namespace BasketPlaner\Bundle\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BasketPlanerTeamBundle:Default:index.html.twig', array('name' => $name));
    }
}
