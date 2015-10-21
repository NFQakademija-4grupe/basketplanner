<?php

namespace BasketPlanner\Bundle\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BasketPlannerTeamBundle:Default:index.html.twig');
    }
}
