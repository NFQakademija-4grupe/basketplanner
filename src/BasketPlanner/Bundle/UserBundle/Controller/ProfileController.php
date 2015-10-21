<?php

namespace BasketPlanner\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProfileController extends Controller
{
    public function editAction()
    {
        return $this->render('BasketPlannerUserBundle:Profile:edit.html.twig', array(
                // ...
            ));    }

    public function showAction()
    {
        return $this->render('BasketPlannerUserBundle:Profile:show.html.twig', array(
                // ...
            ));    }

}
