<?php

namespace BasketPlanner\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TeamController extends Controller
{
    public function indexAction()
    {
        return $this->render('BasketPlannerTeamBundle:Team:index.html.twig');
    }

    public function createAction()
    {
        return $this->render('BasketPlannerTeamBundle:Team:create.html.twig');
    }

    public function editAction()
    {
        return $this->render('BasketPlannerTeamBundle:Team:edit.html.twig');
    }

    public function removeAction()
    {
        return $this->render('BasketPlannerTeamBundle:Team:remove.html.twig');
    }
}
