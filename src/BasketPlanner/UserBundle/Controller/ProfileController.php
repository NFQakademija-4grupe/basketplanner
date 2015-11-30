<?php

namespace BasketPlanner\UserBundle\Controller;

use BasketPlanner\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{

    /**
     * Show user activity
     *
     * @param User $user
     * @return Response
     */
    public function showAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $userProfile = $em->getRepository('BasketPlannerUserBundle:User')->findOneBy(array('id' => $id));

        return $this->render('BasketPlannerUserBundle:Profile:show.html.twig', [
            'user' => $userProfile,
        ]);
    }

}