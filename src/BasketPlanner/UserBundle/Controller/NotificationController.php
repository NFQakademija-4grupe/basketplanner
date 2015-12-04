<?php

namespace BasketPlanner\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{

    const NOTIFICATIONS_PER_PAGE = 12;

    /**
     * Show notifications
     *
     * @param Request $request
     * @param int $page
     * @return Response
     */
    public function showAction(Request $request, $page)
    {
        $em = $this->getDoctrine()
                   ->getEntityManager()->createQueryBuilder();

        $query = $em->select('n')
                    ->from('BasketPlannerUserBundle:Notification', 'n')
                    ->innerJoin('n.notificationUser', 'u')
                    ->where('u.user = :user')
                    ->setParameter('user', $this->getUser());

        $notifications = $this->get('knp_paginator')->paginate(
            $query,
            $request->query->getInt('page', $page),
            self::NOTIFICATIONS_PER_PAGE
        );

        return $this->render('BasketPlannerUserBundle:Notification:show.html.twig', [
          'notifications' => $notifications,
      ]);
    }

}