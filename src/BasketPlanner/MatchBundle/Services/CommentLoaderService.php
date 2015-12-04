<?php

namespace BasketPlanner\MatchBundle\Services;

use BasketPlanner\MatchBundle\Entity\Comment;
use BasketPlanner\MatchBundle\Entity\Match;
use BasketPlanner\MatchBundle\Form\CommentType;
use BasketPlanner\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class CommentLoaderService
{
    private $em;

    private $router;

    private $session;

    private $formFactory;

    public function __construct(EntityManager $em, FormFactory $formFactory, Session $session, Router $router)
    {
        $this->em = $em;
        $this->router = $router;
        $this->session = $session;
        $this->formFactory = $formFactory;
    }

    public function saveComment(Request $request, Match $match, User $user)
    {
        $comment = new Comment();

        $form = $this->formFactory->create(new CommentType(), $comment, [
            'action' => $this->router->generate('basket_planner_comment_create', ['id' => $match->getId()])
        ]);

        $form->handleRequest($request);

        $results['form'] = $form->createView();
        $results['redirectToMatch'] = false;

        if ($form->isValid())
        {

            if (!$match->getPlayers()->contains($user))
            {
                $this->session->getFlashBag()->add('error', 'Tik prisijungę žaidėjai gali rašyti žinutes.');
            }
            else
            {
                $comment->setCreatedAt(new \DateTime('now'));
                $comment->setMatch($match);
                $comment->setUser($user);

                $this->em->persist($comment);
                $this->em->flush();
            }

            $results['redirectToMatch'] = true;
        }

        return $results;
    }
}