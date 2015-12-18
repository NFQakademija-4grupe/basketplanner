<?php

namespace BasketPlanner\TeamBundle\Controller;

use BasketPlanner\TeamBundle\Form\TeamType;
use BasketPlanner\TeamBundle\Form\InviteType;
use BasketPlanner\TeamBundle\Entity\Team;
use BasketPlanner\TeamBundle\Entity\Invite;
use BasketPlanner\UserBundle\Entity\User;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class TeamController extends Controller
{

    public function indexAction(Request $request)
    {
        $invite = new Invite();
        $form = $this->createForm(new InviteType($this->getUser()->getId()), $invite);

        $teamManager = $this->get('basketplanner_team.team_manager');
        $user = $this->getUser()->getId();
        $teams = $teamManager->getUserTeams($user);
        $invites = $teamManager->getUserCreatedInvites($user);

        return $this->render('BasketPlannerTeamBundle:Team:index.html.twig', array(
            'teams' => $teams,
            'invites' => $invites,
            'invite' => $form->createView()
        ));
    }

    public function searchAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $user = $this->getUser()->getId();
            $string = $request->getContent(false);
            $users = $this->getDoctrine()
                ->getRepository('BasketPlannerUserBundle:User')
                ->findByLetters($string, $user);

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

    public function inviteAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $userId = $request->get('user');
            $teamId = $request->get('team');
            $teamManager = $this->get('basketplanner_team.team_manager');
            $em = $this->getDoctrine()->getEntityManager();

            $message = '';
            $status = 'failed';

            $teamPlayers = $teamManager->getTeamPlayersCount($teamId);
            $teamPlayersLimit = $teamManager->getTeamPlayersLimit($teamId);

            if ($teamPlayers < $teamPlayersLimit)
            {
                $inviteExists = $em->getRepository('BasketPlannerTeamBundle:Invite')->findOneBy(array(
                    'team' => $teamId,
                    'user' => $userId
                ));

                if ($inviteExists === null)
                {
                    $user = $em->getRepository('BasketPlannerUserBundle:User')->find($userId);
                    $team = $em->getRepository('BasketPlannerTeamBundle:Team')->find($teamId);

                    if ($user != null && $team != null)
                    {
                        $invite = new Invite();
                        $invite->setStatus('New');
                        $invite->setUser($user);
                        $invite->setTeam($team);
                        $invite->setCreated(new \DateTime('now'));

                        $em->persist($user);
                        $em->persist($team);
                        $em->persist($invite);
                        $em->flush();

                        $message = 'Vartotojas sekmingai pakviestas į pasirinktą komandą!';
                        $status = 'ok';
                    } else {
                        $message = 'Įvyko klaida!! Nepavyko rasti nurodytos komandos arba vartotojo!';
                    }
                } else {
                    $message = 'Įvyko klaida! Šis vartotojas jau pakviestas į pasirinktą komandą!';
                }
            } else {
                $message = 'Įvyko klaida! Komanda jau pilna!';
            }

            $response = json_encode(array('message' => $message, 'status' => $status));

            return new Response($response, 200, array(
                'Content-Type' => 'application/json'
            ));
        } else {
            $response = json_encode(array('message' => 'Jūs neturite priegos!', 'status' => 'failed'));

            return new Response($response, 400, array(
                'Content-Type' => 'application/json'
            ));
        }
    }

    public function inviteDeleteAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $userId = $this->getUser()->getId();
            $inviteId = $request->get('inviteId');
            $teamManager = $this->get('basketplanner_team.team_manager');
            $em = $this->getDoctrine()->getEntityManager();

            $invite = $em->getRepository('BasketPlannerTeamBundle:Invite')->find($inviteId);
            $userRole = $teamManager->getUserRoleInTeam($userId, $invite->getTeam()->getId());

            $message = '';
            $status = 'failed';

            if ($userRole == 'Owner'){

                $message = 'Pakvietimas pašalintas!';
                $status = 'ok';
            }else {
                $message = 'Pakvietimo pašalinti nepavyko!';
            }

            $response = json_encode(array('message' => $message, 'status' => $status ));

            return new Response($response, 200, array(
                'Content-Type' => 'application/json'
            ));
        } else {
            $response = json_encode(array('message' => 'Jūs neturite priegos!', 'status' => 'failed'));

            return new Response($response, 400, array(
                'Content-Type' => 'application/json'
            ));
        }
    }

}
