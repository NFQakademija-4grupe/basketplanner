<?php

namespace BasketPlanner\TeamBundle\Controller;

use BasketPlanner\TeamBundle\Form\TeamType;
use BasketPlanner\TeamBundle\Entity\Team;
use BasketPlanner\TeamBundle\Entity\TeamUsers;
use BasketPlanner\MatchBundle\Entity\Type;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TeamController extends Controller
{

    public function indexAction()
    {
        return $this->render('BasketPlannerTeamBundle:Team:index.html.twig', array(
            'message' => 'Hello',
        ));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {

        $team = new Team();

        $form = $this->createForm(new TeamType(), $team);

        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form->isValid()) {

            $team->setCreated(new \DateTime('now'));

            $user = $this->getUser();

            $teamPlayer = new TeamUsers();
            $teamPlayer->setUser($user);
            $teamPlayer->setTeam($team);
            $teamPlayer->setRole('Owner');

            $em->persist($team);
            $em->persist($teamPlayer);
            $em->flush();

            return $this->redirectToRoute('basket_planner_team_show', ['id' => $team->getId()]);
        }

        return $this->render('BasketPlannerTeamBundle:Team:create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function showAction(Team $team)
    {
        $em = $this->getDoctrine()->getManager();
        $players = $em->createQuery("
            SELECT u.id, u.firstName, u.lastName, u.profilePicture, tp.role
                FROM BasketPlannerTeamBundle:TeamUsers tp
                JOIN BasketPlannerUserBundle:User u
                WITH tp.user = u.id
                WHERE tp.team = ".$team->getId())->getArrayResult();
        return $this->render('BasketPlannerTeamBundle:Team:show.html.twig', [
                'team' => $team,
                'players' => $players,
        ]);
    }
}
