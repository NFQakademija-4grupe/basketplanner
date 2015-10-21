<?php

namespace BasketPlanner\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BasketPlannerMainBundle:Default:index.html.twig');
    }
}
