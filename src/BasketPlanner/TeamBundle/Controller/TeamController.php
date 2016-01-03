<?php

namespace BasketPlanner\TeamBundle\Controller;

use BasketPlanner\TeamBundle\Entity\TeamUser;
use BasketPlanner\TeamBundle\Form\TeamType;
use BasketPlanner\TeamBundle\Form\InviteType;
use BasketPlanner\TeamBundle\Entity\Team;
use BasketPlanner\TeamBundle\Entity\Invite;
use BasketPlanner\UserBundle\Entity\User;
use BasketPlanner\TeamBundle\Exception\TeamException;
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

        $teamRepository = $this->get('doctrine.orm.entity_manager')->getRepository('BasketPlannerTeamBundle:Team');
        $inviteRepository = $this->get('doctrine.orm.entity_manager')->getRepository('BasketPlannerTeamBundle:Invite');
        $user = $this->getUser()->getId();

        $teams = $teamRepository->getUserTeams($user);
        $createdInvites = $inviteRepository->getUserCreatedInvites($user);
        $receivedInvites = $inviteRepository->getUserReceivedInvites($user);

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
        $teamRepository = $this->get('doctrine.orm.entity_manager')->getRepository('BasketPlannerTeamBundle:Team');
        $players = $teamRepository->getTeamPlayers($team->getId());

        return $this->render('BasketPlannerTeamBundle:Team:show.html.twig', [
                'team' => $team,
                'players' => $players,
        ]);
    }

    public function leaveAction(Request $request)
    {
        $teamManager = $this->get('basketplanner_team.team_manager');
        if ($request->isXmlHttpRequest()) {
            $userId = $this->getUser()->getId();
            $teamId = intval(strip_tags($request->request->get('id')));

            try {
                $teamManager->leaveTeam($userId, $teamId);

                return $teamManager->createJSonResponse('Jūs sekmingai palikote komandą!', 'ok', 200);

            } catch (TeamException $e){

                return $teamManager->createJSonResponse($e->getMessage(), 'failed', 200);

            }
        } else {

            return $teamManager->createJSonResponse('Jūs neturite priegos!', 'failed', 400);

        }
    }

    public function deleteAction(Request $request)
    {
        $teamManager = $this->get('basketplanner_team.team_manager');
        if ($request->isXmlHttpRequest())
        {
            $userId = $this->getUser()->getId();
            $teamId = intval(strip_tags($request->request->get('id')));

            try {
                $teamManager->deleteTeam($userId, $teamId);

                return $teamManager->createJSonResponse('Jūs sekmingai pašalinote komandą!', 'ok', 200);

            } catch (TeamException $e) {

                return $teamManager->createJSonResponse($e->getMessage(), 'failed', 200);

            }
        } else {

            return $teamManager->createJSonResponse('Jūs neturite priegos!', 'failed', 400);

        }
    }

    public function inviteAction(Request $request)
    {
        $teamManager = $this->get('basketplanner_team.team_manager');
        if ($request->isXmlHttpRequest()) {
            $userId = intval(strip_tags($request->request->get('user')));
            $invitedId = $this->getUser()->getId();
            $teamId = intval(strip_tags($request->request->get('team')));

            try {
                $teamManager->inviteToTeam($userId, $invitedId, $teamId);

                return $teamManager->createJSonResponse('Vartotojas sekmingai pakviestas į pasirinktą komandą!', 'ok', 200);

            } catch (TeamException $e) {

                return $teamManager->createJSonResponse($e->getMessage(), 'failed', 200);

            }
        } else {

            return $teamManager->createJSonResponse('Jūs neturite priegos!', 'failed', 400);

        }
    }

    public function inviteDeleteAction(Request $request)
    {
        $teamManager = $this->get('basketplanner_team.team_manager');
        if ($request->isXmlHttpRequest()) {
            $userId = $this->getUser()->getId();
            $inviteId = intval(strip_tags($request->request->get('id')));

            try {
                $teamManager->inviteDelete($userId, $inviteId);

                return $teamManager->createJSonResponse('Pakvietimas sekmingai pašalintas!', 'ok', 200);

            } catch (TeamException $e) {

                return $teamManager->createJSonResponse($e->getMessage(), 'failed', 200);

            }
        } else {

            return $teamManager->createJSonResponse('Jūs neturite priegos!', 'failed', 400);

        }
    }

    public function inviteAcceptAction(Request $request)
    {
        $teamManager = $this->get('basketplanner_team.team_manager');
        if ($request->isXmlHttpRequest())
        {
            $userId = $this->getUser()->getId();
            $inviteId = intval(strip_tags($request->request->get('id')));
            $limit = $this->container->getParameter('basket_planner_team.joined_teams_limit');

            try {
                $teamManager->inviteAccept($userId, $inviteId, $limit);

                return $teamManager->createJSonResponse('Jūs sėkmingai prisijungėte prie komandos!', 'ok', 200);

            } catch (TeamException $e) {

                return $teamManager->createJSonResponse($e->getMessage(), 'failed', 200);

            }
        } else {

            return $teamManager->createJSonResponse('Jūs neturite priegos!', 'failed', 400);

        }
    }

    public function inviteChangeStatusAction(Request $request)
    {
        $teamManager = $this->get('basketplanner_team.team_manager');
        if ($request->isXmlHttpRequest()) {
            $userId = $this->getUser()->getId();
            $inviteId = intval(strip_tags($request->request->get('id')));
            $inviteStatus = strip_tags($request->request->get('status'));

            try {
                $teamManager->inviteChangeStatus($userId, $inviteId, $inviteStatus);
                $msg = '';
                if ($inviteStatus == 'Seen'){
                    $msg = 'Pakvietimo statusas pakeistas!';
                } else if ($inviteStatus == 'Rejected'){
                    $msg = 'Pakvietimas sekmingai atmestas!';
                }

                return $teamManager->createJSonResponse($msg, 'ok', 200);

            } catch (TeamException $e) {

                return $teamManager->createJSonResponse($e->getMessage(), 'failed', 200);

            }
        } else {

            return $teamManager->createJSonResponse('Jūs neturite priegos!', 'failed', 400);

        }
    }
}
