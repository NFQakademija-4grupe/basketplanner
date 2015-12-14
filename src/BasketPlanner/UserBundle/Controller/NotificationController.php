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
                   ->getManager()->createQueryBuilder();

        $query = $em->select('n, u.seen')
                    ->from('BasketPlannerUserBundle:Notification', 'n')
                    ->leftJoin('BasketPlanner\UserBundle\Entity\NotificationUser',
                        'u',
                        \Doctrine\ORM\Query\Expr\Join::WITH,
                        'n.id = u.notification')
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

    public function deleteAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');

            $em = $this->getDoctrine()->getManager();

            $repository = $em->getRepository('BasketPlannerUserBundle:NotificationUser');
            $notifiedUser = $repository->findOneBy(array('notification'=> $id, 'user'=> $this->getUser()->getId() ));

            $query = $em->createQuery('SELECT COUNT(n.id) FROM BasketPlanner\UserBundle\Entity\NotificationUser n WHERE n.notification ='.$id);
            $count = $query->getSingleScalarResult();

            if($count == 1 ){
                $notificationRepository = $em->getRepository('BasketPlannerUserBundle:Notification');
                $notification = $notificationRepository->findOneBy(array('id' => $id));

                $em->remove($notifiedUser);
                $em->remove($notification);
                $em->flush();
            }else if($count > 1){
                $em->remove($notifiedUser);
                $em->flush();
            }

            $response = json_encode(array('message' => 'Pranešimas ištrintas'));

            return new Response($response, 200, array(
                'Content-Type' => 'application/json'
            ));
        } else {
            $response = json_encode(array('message' => 'Jūs neturite priegos!'));

            return new Response($response, 400, array(
                'Content-Type' => 'application/json'
            ));
        }
    }

    public function updateStatusAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');

            $em = $this->getDoctrine()->getManager();

            $repository = $em->getRepository('BasketPlannerUserBundle:NotificationUser');
            $notifiedUser = $repository->findOneBy(array('notification'=> $id, 'user'=> $this->getUser()->getId() ));
            $notifiedUser->setSeen(true);
            $em->persist($notifiedUser);
            $em->flush();

            $response = json_encode(array('message' => 'Pranešimo statusas pakeistas!'));

            return new Response($response, 200, array(
                'Content-Type' => 'application/json'
            ));
        }else{
            $response = json_encode(array('message' => 'Jūs neturite priegos!'));

            return new Response($response, 400, array(
                'Content-Type' => 'application/json'
            ));
        }
    }

}