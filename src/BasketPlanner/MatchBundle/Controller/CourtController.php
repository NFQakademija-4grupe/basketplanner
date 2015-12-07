<?php

namespace BasketPlanner\MatchBundle\Controller;

use BasketPlanner\MatchBundle\Entity\Match;
use BasketPlanner\MatchBundle\Form\CourtType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CourtController extends Controller
{

    /**
     * Show court
     */
    public function indexAction()
    {
        $loadMap = $this->get('basketplanner_match.map_loader_service');
        $map = $loadMap->loadMarkers(false);

        return $this->render('BasketPlannerMatchBundle:Court:index.html.twig', [
            'map' => $map,
        ]);
    }

    public function updateApprovedAction(Request $request){
        if($request->isXmlHttpRequest()) {
            $id = $request->get('id');

            $em = $this->getDoctrine()->getManager();

            $repository = $em->getRepository('BasketPlannerMatchBundle:Court');
            $court = $repository->findOneBy(array('id'=> $id));
            $court->setApproved(true);
            $em->persist($court);
            $em->flush();

            $response = json_encode(array('approved' => 'yes'));

            return new Response($response, 200, array(
                'Content-Type' => 'application/json'
            ));
        }

        $response = json_encode(array('approved' => 'no'));

        return new Response($response, 200, array(
            'Content-Type' => 'application/json'
        ));
    }
}