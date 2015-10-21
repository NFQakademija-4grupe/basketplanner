<?php

namespace BasketPlanner\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function indexAction()
    {
        return $this->render('BasketPlannerMainBundle:Main:index.html.twig');
    }
}
