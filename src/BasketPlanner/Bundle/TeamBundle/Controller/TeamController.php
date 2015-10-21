<?php

namespace BasketPlanner\Bundle\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TeamController extends Controller
{
    public function indexAction()
    {
        return $this->render('BasketPlannerTeamBundle:Team:index.html.twig');
    }
}
