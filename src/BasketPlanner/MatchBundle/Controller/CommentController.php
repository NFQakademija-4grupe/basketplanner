<?php

namespace BasketPlanner\MatchBundle\Controller;

use BasketPlanner\MatchBundle\Entity\Comment;
use BasketPlanner\MatchBundle\Entity\Match;
use BasketPlanner\MatchBundle\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends Controller
{
    public function createAction(Request $request, Match $match)
    {
        $commentLoader = $this->get('basketplanner_match.comment_loader_service');
        $data = $commentLoader->saveComment($request, $match, $this->getUser());

        if ($data['redirectToMatch']) {
            return $this->redirectToRoute('basket_planner_match_show', ['id' => $match->getId()]);
        }

        return $this->render('BasketPlannerMatchBundle:Comment:create.html.twig', ['form' => $data['form']]);
    }
}