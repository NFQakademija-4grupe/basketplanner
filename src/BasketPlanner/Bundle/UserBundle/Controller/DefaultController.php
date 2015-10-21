<?php

namespace BasketPlanner\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BasketPlannerUserBundle:Default:index.html.twig');
    }
}
