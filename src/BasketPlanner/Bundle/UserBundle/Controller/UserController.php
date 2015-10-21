<?php

namespace BasketPlanner\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    public function indexAction()
    {
        return $this->render('BasketPlannerUserBundle:User:index.html.twig');
    }
}
