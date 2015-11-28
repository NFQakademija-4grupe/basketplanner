<?php

namespace BasketPlanner\MatchBundle\Controller;

use BasketPlanner\MatchBundle\Entity\Match;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ActivityController extends Controller
{

    /**
     * Show user activity
     */
    public function indexAction()
    {
        $activityLoader = $this->get('basketplanner_match.user_activity_service');
        $userId = $this->getUser()->getId();
        $userCreatedMatches = $activityLoader->loadUserCreatedMatches($userId, 5);
        $userAttendedMatches = $activityLoader->loadUserAttendedMatches($this->getUser(), 5);

        return $this->render('BasketPlannerMatchBundle:Activity:index.html.twig', [
            'createdMatches' => $userCreatedMatches,
            'attendedMatches' => $userAttendedMatches,
        ]);
    }

    /**
     * Show user created matches
     */
    public function listCreatedAction()
    {
        return $this->render('BasketPlannerMatchBundle:Activity:created.html.twig');
    }

    /**
     * Show user attended matches
     */
    public function listAttendedAction()
    {
        return $this->render('BasketPlannerMatchBundle:Activity:attended.html.twig');
    }
}