<?php

namespace BasketPlanner\Bundle\MatchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BasketPlannerMatchBundle:Default:index.html.twig');
    }
}
