<?php

namespace BasketPlanner\TeamBundle\Controller;

use BasketPlanner\TeamBundle\Entity\TeamUser;
use BasketPlanner\TeamBundle\Form\TeamType;
use BasketPlanner\TeamBundle\Form\InviteType;
use BasketPlanner\TeamBundle\Entity\Team;
use BasketPlanner\TeamBundle\Entity\Invite;
use BasketPlanner\UserBundle\Entity\User;
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
        $createdInvites = $teamManager->getUserCreatedInvites($user);
        $receivedInvites = $teamManager->getUserReceivedInvites($user);

        return $this->render('BasketPlannerTeamBundle:Team:index.html.twig', array(
            'teams' => $teams,
            'createdInvites' => $createdInvites,
            'receivedInvites' => $receivedInvites,
            'invite' => $form->createView()
        ));
    }

    public function searchAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $user = $this->getUser()->getId();
            $string = strip_tags($request->getContent(false));
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

                return $this->redirectToRoute('basket_planner_team.show', ['id' => $team->getId()]);

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

    public function leaveAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $message = '';
            $status = 'failed';
            $userId = $this->getUser()->getId();
            $teamId = strip_tags($request->request->get('teamId'));
            $teamManager = $this->get('basketplanner_team.team_manager');
            $em = $this->getDoctrine()->getEntityManager();
            $userRole = $teamManager->getUserRoleInTeam($userId, $teamId);

            if ($userRole != null){
                if($userRole != 'Owner'){
                    $teamUser = $em->getRepository('BasketPlannerTeamBundle:TeamUser')->findOneBy(array(
                        'team' => $teamId,
                        'user' => $userId
                    ));

                    $em->remove($teamUser);
                    $em->flush();
                    $message = 'Jūs sekmingai palikote komandą!';
                    $status = 'ok';
                }else{
                    $message = 'Komandos sąvininkas negali palikti komandos!';
                }
            }else{
                $message = 'Jūs neturite priegos!';
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

    public function deleteAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $message = '';
            $status = 'failed';
            $userId = $this->getUser()->getId();
            $teamId = strip_tags($request->request->get('teamId'));
            $teamManager = $this->get('basketplanner_team.team_manager');
            $em = $this->getDoctrine()->getEntityManager();
            $userRole = $teamManager->getUserRoleInTeam($userId, $teamId);

            if ($userRole != null)
            {
                if($userRole == 'Owner')
                {
                    //TO DO: notifications, check for active matches
                    $team = $em->getRepository('BasketPlannerTeamBundle:Team')->find($teamId);
                    $teamPlayers = $team->getTeamUser();

                    foreach ($teamPlayers as $player)
                    {
                        $em->remove($player);
                    }

                    $em->remove($team);
                    $em->flush();
                    $message = 'Komanda sekmingai pašalinta!';
                    $status = 'ok';
                }else{
                    $message = 'Komandą ištrinti gali tik jos sąvininkas!';
                }
            }else{
                $message = 'Jūs neturite priegos!';
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

    public function inviteAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $message = '';
            $status = 'failed';
            $userId = strip_tags($request->request->get('user'));
            $teamId = strip_tags($request->request->get('team'));
            $teamManager = $this->get('basketplanner_team.team_manager');
            $em = $this->getDoctrine()->getEntityManager();

            //check if user is not a member of team
            if ($teamManager->getUserRoleInTeam($userId, $teamId) === null)
            {
                //check if team is not full
                if ($teamManager->getTeamPlayersCount($teamId) < $teamManager->getTeamPlayersLimit($teamId))
                {
                    $inviteExists = $em->getRepository('BasketPlannerTeamBundle:Invite')->findOneBy(array(
                        'team' => $teamId,
                        'user' => $userId
                    ));

                    //check if invite to same user and team already exists
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
            } else {
                $message = 'Įvyko klaida! Šis žaidėjas jau yra šioje komandoje!';
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
            $message = '';
            $status = 'failed';
            $userId = $this->getUser()->getId();
            $inviteId = strip_tags($request->request->get('inviteId'));
            $teamManager = $this->get('basketplanner_team.team_manager');
            $em = $this->getDoctrine()->getEntityManager();

            $invite = $em->getRepository('BasketPlannerTeamBundle:Invite')->find($inviteId);
            $userRole = $teamManager->getUserRoleInTeam($userId, $invite->getTeam()->getId());

            if ($invite !== null) {
                if ($userRole == 'Owner') {
                    $em->remove($invite);
                    $em->flush();
                    $message = 'Pakvietimas pašalintas!';
                    $status = 'ok';
                } else {
                    $message = 'Pakvietimo pašalinti nepavyko!';
                }
            } else {
                $message = 'Pakvietimo rasti nepavyko!';
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

    public function inviteAcceptAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $message = '';
            $status = 'failed';
            $userId = $this->getUser()->getId();
            $inviteId = strip_tags($request->request->get('inviteId'));
            $teamManager = $this->get('basketplanner_team.team_manager');
            $em = $this->getDoctrine()->getEntityManager();

            $invite = $em->getRepository('BasketPlannerTeamBundle:Invite')->find($inviteId);

            if($invite !== null)
            {
                if ($invite->getUser()->getId() === $userId)
                {
                    $teamId = $invite->getTeam()->getId();
                    if ($teamManager->getTeamPlayersCount($teamId) < $teamManager->getTeamPlayersLimit($teamId))
                    {
                        $user = $em->getRepository('BasketPlannerUserBundle:User')->find($userId);
                        $team = $em->getRepository('BasketPlannerTeamBundle:Team')->find($teamId);

                        $teamUser = new TeamUser();
                        $teamUser->setUser($user);
                        $teamUser->setTeam($team);
                        $teamUser->setRole('Player');

                        //TO DO: notificate team players about new user
                        $em->persist($teamUser);
                        $em->remove($invite);
                        $em->flush();

                        $message = 'Jūs sėkmingai prisijungėte prie komandos!';
                        $status = 'ok';
                    } else {
                        $message = 'Pasiektas komandos žaidėjų limitas!';
                    }
                }else{
                    $message = 'Jūs neturite priegos!';
                }
            }else{
                $message = 'Jūs neturite priegos!';
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

    public function inviteChangeStatusAction(Request $request){
        if ($request->isXmlHttpRequest()) {
            $message = '';
            $status = 'failed';
            $inviteId = strip_tags($request->request->get('inviteId'));
            $inviteStatus = strip_tags($request->request->get('status'));
            $em = $this->getDoctrine()->getEntityManager();
            $invite = $em->getRepository('BasketPlannerTeamBundle:Invite')->find($inviteId);

            if($invite->getUser()->getId() == $this->getUser()->getId()){

                if($inviteStatus == 'Seen'){
                    $invite->setStatus('Seen');
                }else if ($inviteStatus == 'Rejected'){
                    $invite->setStatus('Rejected');
                }

                //TO DO: notificate user about status change
                $em->persist($invite);
                $em->flush();
                $message = 'Pakvietimas atmestas!';
                $status = 'ok';
            }else{
                $message = 'Jūs neturite priegos!';
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
