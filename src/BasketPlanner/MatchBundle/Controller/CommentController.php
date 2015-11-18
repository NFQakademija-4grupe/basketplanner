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
        $comment = new Comment();

        $form = $this->createForm(new CommentType(), $comment, [
            'action' => $this->generateUrl('basket_planner_comment_create', ['id' => $match->getId()])
        ]);

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $comment->setCreatedAt(new \DateTime('now'));
            $comment->setMatch($match);
            $comment->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('basket_planner_match_show', ['id' => $match->getId()]);
        }

        return $this->render('BasketPlannerMatchBundle:Comment:create.html.twig', ['form' => $form->createView()]);
    }
}