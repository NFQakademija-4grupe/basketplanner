<?php

namespace BasketPlanner\TeamBundle\Controller;

use BasketPlanner\TeamBundle\Form\TeamType;
use BasketPlanner\TeamBundle\Entity\Team;
use BasketPlanner\TeamBundle\Entity\TeamUser;
use BasketPlanner\MatchBundle\Entity\Type;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TeamController extends Controller
{

    public function indexAction()
    {
        $teamService = $this->get('basketplanner_team.team_service');
        $teams = $teamService->getUserTeams($this->getUser()->getId());

        return $this->render('BasketPlannerTeamBundle:Team:index.html.twig', array(
            'teams' => $teams,
        ));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $customErrors = [];
        $team = new Team();

        $form = $this->createForm(new TeamType(), $team);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form->isValid()) {
            $teamService = $this->get('basketplanner_team.team_service');
            $limit = $this->container->getParameter('basket_planner_team.created_teams_limit');

            if ($teamService->getUserCreatedTeamsCount($this->getUser()->getId()) <= $limit ){
                $team->setCreated(new \DateTime('now'));
                $user = $this->getUser();
                $teamPlayer = $teamService->createTeamPlayer($user, $team, 'Owner');

                $em->persist($team);
                $em->persist($teamPlayer);
                $em->flush();

                return $this->redirectToRoute('basket_planner_team_show', ['id' => $team->getId()]);

            }else{
                $error = array(
                    "name" => "Komandų limitas",
                    "message" => "Jūs pasiekėte leidžiamų sukurti komandų limitą."
                );
                $customErrors [] = $error;
            }
        }

        return $this->render('BasketPlannerTeamBundle:Team:create.html.twig', [
            'form' => $form->createView(),
            'customErrors' => $customErrors
        ]);
    }

    public function showAction(Team $team)
    {
        $em = $this->getDoctrine()->getManager();
        $players = $em->createQuery("
            SELECT u.id, u.firstName, u.lastName, u.profilePicture, tp.role
                FROM BasketPlannerTeamBundle:TeamUser tp
                JOIN BasketPlannerUserBundle:User u
                WITH tp.user = u.id
                WHERE tp.team = ".$team->getId())->getArrayResult();
        return $this->render('BasketPlannerTeamBundle:Team:show.html.twig', [
                'team' => $team,
                'players' => $players,
        ]);
    }
}
