<?php

namespace BasketPlanner\UserBundle\Controller;

use BasketPlanner\UserBundle\Entity\User;
use BasketPlanner\UserBundle\Entity\Notification;
use BasketPlanner\UserBundle\Entity\NotificationUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{

    /**
     * Show user activity
     *
     * @return Response
     */
    public function showAction()
    {
        $notifications = $this->get('basketplanner_user.notification.service');

        return $this->render('BasketPlannerUserBundle:Notification:show.html.twig');
    }

}