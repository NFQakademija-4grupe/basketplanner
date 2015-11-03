<?php

namespace BasketPlanner\MatchBundle\Controller;

use BasketPlanner\MatchBundle\Entity\Match;
use BasketPlanner\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;

class MatchController extends Controller
{
    public function createAction()
    {
        $user = new User();

        $match = new Match();
        $match->setDescription('Sample discription');
        $match->setBeginsAt(new \DateTime('2015-11-04 11:14:15'));
        $match->setLatitude(24.65874);
        $match->setLongitude(39.58458);
        //$match->setUser($user);
        $match->setCreatedAt(new \DateTime(time()));
        $match->setDistrict('sdfsdfdsfdsf');
        $match->setType('asdsads');

        $em = $this->getDoctrine()->getManager();

        $em->persist($match);
        $em->flush();

        return new Response('OK');
    }

    public function showAction()
    {
        $match = $this->getDoctrine()
            ->getRepository('BasketPlannerMatchBundle:Match')
            ->find(2);
        $username = $match->getUser()->getFirstName();
        return new Response($username);
    }
}