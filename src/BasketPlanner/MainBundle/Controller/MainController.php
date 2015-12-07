<?php

namespace BasketPlanner\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class MainController extends Controller
{
    public function indexAction()
    {
        $auth_checker = $this->get('security.authorization_checker');
        $isRoleUser = $auth_checker->isGranted('ROLE_USER');

        if ($isRoleUser) {
            return $this->redirectToRoute('basket_planner_match_list');
        }

        $matchLoader = $this->get('basketplanner_match.match_loader_service');
        $latestMatches = $matchLoader->getLatest(3);

        return $this->render('BasketPlannerMainBundle:Main:index.html.twig', ['matches' => $latestMatches]);
    }
}
