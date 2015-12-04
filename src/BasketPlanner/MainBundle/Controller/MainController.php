<?php

namespace BasketPlanner\MainBundle\Controller;

use BasketPlanner\MainBundle\Entity\CronTask;
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

    public function cronAction()
    {
        $entity = new CronTask();

        $entity
            ->setName('Upcoming matches check')
            ->setInterval(3600) // Run once every hour
            ->setCommands(array(
                'match:check-upcoming'
            ));

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new Response('OK!');
    }
}
