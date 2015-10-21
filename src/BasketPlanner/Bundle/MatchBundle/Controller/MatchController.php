<?php

namespace BasketPlanner\Bundle\MatchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MatchController extends Controller
{
    public function indexAction()
    {
        return $this->render('BasketPlannerMatchBundle:Match:index.html.twig');
    }

    public function createAction()
    {
        return $this->render('BasketPlannerMatchBundle:Match:create.html.twig');
    }

    public function showAction()
    {
        return $this->render('BasketPlannerMatchBundle:Match:show.html.twig');
    }

    public function joinAction()
    {
        return $this->render('BasketPlannerMatchBundle:Match:join.html.twig');
    }

    public function cancelAction()
    {
        return $this->render('BasketPlannerMatchBundle:Match:cancel.html.twig');
    }
}
