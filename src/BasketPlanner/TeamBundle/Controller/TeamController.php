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
        $teamManager = $this->get('basketplanner_team.team_manager');
        if ($request->isXmlHttpRequest()) {
            $user = $this->getUser()->getId();
            $string = strip_tags($request->getContent(false));
            $users = $this->getDoctrine()
                ->getRepository('BasketPlannerUserBundle:User')
                ->findByLetters($string, $user);

            return new JsonResponse($users);

        } else {

            return $teamManager->createJSonResponse('Jūs neturite priegos!', 'failed', 400);

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

            //check if user reached limit of teams allowed to have
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
        $teamManager = $this->get('basketplanner_team.team_manager');
        if ($request->isXmlHttpRequest()) {
            $message = '';
            $status = 'failed';
            $userId = $this->getUser()->getId();
            $teamId = intval(strip_tags($request->request->get('id')));
            $em = $this->getDoctrine()->getEntityManager();
            $userRole = $teamManager->getUserRoleInTeam($userId, $teamId);

            //check if user is part of the team
            if ($userRole != null){
                //check if user is owner of the team
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

            return $teamManager->createJSonResponse($message, $status, 200);

        } else {

            return $teamManager->createJSonResponse('Jūs neturite priegos!', 'failed', 400);

        }
    }

    public function deleteAction(Request $request)
    {
        $teamManager = $this->get('basketplanner_team.team_manager');
        if ($request->isXmlHttpRequest())
        {
            $message = '';
            $status = 'failed';
            $userId = $this->getUser()->getId();
            $teamId = intval(strip_tags($request->request->get('id')));
            $em = $this->getDoctrine()->getEntityManager();
            $userRole = $teamManager->getUserRoleInTeam($userId, $teamId);

            //check if user is part of the team and is owner of the team
            if ($userRole != null && $userRole == 'Owner')
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
                $message = 'Jūs neturite priegos!';
            }

            return $teamManager->createJSonResponse($message, $status, 200);

        } else {

            return $teamManager->createJSonResponse('Jūs neturite priegos!', 'failed', 400);

        }
    }

    public function inviteAction(Request $request)
    {
        $teamManager = $this->get('basketplanner_team.team_manager');
        if ($request->isXmlHttpRequest()) {
            $message = '';
            $status = 'failed';
            $userId = intval(strip_tags($request->request->get('user')));
            $teamId = intval(strip_tags($request->request->get('team')));
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
                            $invite = $teamManager->createTeamInvite($user, $team);

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

            return $teamManager->createJSonResponse($message, $status, 200);

        } else {

            return $teamManager->createJSonResponse('Jūs neturite priegos!', 'failed', 400);

        }
    }

    public function inviteDeleteAction(Request $request)
    {
        $teamManager = $this->get('basketplanner_team.team_manager');
        if ($request->isXmlHttpRequest()) {
            $message = '';
            $status = 'failed';
            $userId = $this->getUser()->getId();
            $inviteId = intval(strip_tags($request->request->get('id')));
            $em = $this->getDoctrine()->getEntityManager();

            $invite = $em->getRepository('BasketPlannerTeamBundle:Invite')->find($inviteId);
            $userRole = $teamManager->getUserRoleInTeam($userId, $invite->getTeam()->getId());

            //check if such invite exist
            if ($invite !== null) {
                //check if user have privileges to remove invite
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

            return $teamManager->createJSonResponse($message, $status, 200);

        } else {

            return $teamManager->createJSonResponse('Jūs neturite priegos!', 'failed', 400);

        }
    }

    public function inviteAcceptAction(Request $request)
    {
        $teamManager = $this->get('basketplanner_team.team_manager');
        if ($request->isXmlHttpRequest())
        {
            $message = '';
            $status = 'failed';
            $userId = $this->getUser()->getId();
            $inviteId = intval(strip_tags($request->request->get('id')));
            $em = $this->getDoctrine()->getEntityManager();

            $invite = $em->getRepository('BasketPlannerTeamBundle:Invite')->find($inviteId);

            if($invite !== null)
            {
                //check if current user is user which have been invited
                if ($invite->getUser()->getId() === $userId)
                {
                    $teamId = $invite->getTeam()->getId();
                    //check if is not full
                    if ($teamManager->getTeamPlayersCount($teamId) < $teamManager->getTeamPlayersLimit($teamId))
                    {
                        $user = $em->getRepository('BasketPlannerUserBundle:User')->find($userId);
                        $team = $em->getRepository('BasketPlannerTeamBundle:Team')->find($teamId);

                        $teamUser = $teamManager->createTeamPlayer($user, $team, 'Player');

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

            return $teamManager->createJSonResponse($message, $status, 200);

        } else {

            return $teamManager->createJSonResponse('Jūs neturite priegos!', 'failed', 400);

        }
    }

    public function inviteChangeStatusAction(Request $request)
    {
        $teamManager = $this->get('basketplanner_team.team_manager');
        if ($request->isXmlHttpRequest()) {
            $message = '';
            $status = 'failed';
            $inviteId = intval(strip_tags($request->request->get('id')));
            $inviteStatus = strip_tags($request->request->get('status'));
            $em = $this->getDoctrine()->getEntityManager();
            $invite = $em->getRepository('BasketPlannerTeamBundle:Invite')->find($inviteId);

            //check if current user is user which have been invited
            if($invite->getUser()->getId() == $this->getUser()->getId()){

                if($inviteStatus == 'Seen'){
                    $invite->setStatus('Seen');
                    $message = 'Pakvietimo statusas pakeistas!';
                }else if ($inviteStatus == 'Rejected'){
                    $invite->setStatus('Rejected');
                    $message = 'Pakvietimas atmestas!';
                }

                //TO DO: notificate user about status change
                $em->persist($invite);
                $em->flush();
                $status = 'ok';
            }else{
                $message = 'Jūs neturite priegos!';
            }

            return $teamManager->createJSonResponse($message, $status, 200);

        } else {

            return $teamManager->createJSonResponse('Jūs neturite priegos!', 'failed', 400);

        }
    }
}
