<?php

namespace BasketPlanner\Bundle\MatchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MatchController extends Controller
{
    public function indexAction()
    {
        return $this->render('BasketPlannerMatchBundle:Match:index.html.twig');
    }
}
