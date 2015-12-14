<?php

namespace BasketPlanner\TeamBundle\Controller;

use BasketPlanner\TeamBundle\Form\TeamType;
use BasketPlanner\TeamBundle\Entity\Team;
use BasketPlanner\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class TeamController extends Controller
{

    public function indexAction()
    {
        $teamManager = $this->get('basketplanner_team.team_manager');
        $user = $this->getUser()->getId();
        $teams = $teamManager->getUserTeams($user);

        return $this->render('BasketPlannerTeamBundle:Team:index.html.twig', array(
            'teams' => $teams,
        ));
    }

    public function searchAction(Request $request)
    {
        die(var_dump('asd'));
        if ($request->isXmlHttpRequest()) {
            $string = $request->get('searchText');
            $users = $this->getDoctrine()
                ->getRepository('BasketPlannerUserBundle:User')
                ->findByLetters($string);

            return new JsonResponse($users);
        } else {
            $response = json_encode(array('message' => 'Jūs neturite priegos!'));

            return new Response($response, 400, array(
                'Content-Type' => 'application/json'
            ));
        }
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
            $teamManager = $this->get('basketplanner_team.team_manager');
            $limit = $this->container->getParameter('basket_planner_team.created_teams_limit');

            if ($teamManager->getUserTeamsCount($this->getUser()->getId(), 'Owner') <= $limit ){
                $team->setCreated(new \DateTime('now'));
                $user = $this->getUser();
                $teamPlayer = $teamManager->createTeamPlayer($user, $team, 'Owner');

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
        $teamManager = $this->get('basketplanner_team.team_manager');
        $players = $teamManager->getTeamPlayers($team->getId());
        return $this->render('BasketPlannerTeamBundle:Team:show.html.twig', [
                'team' => $team,
                'players' => $players,
        ]);
    }
}
