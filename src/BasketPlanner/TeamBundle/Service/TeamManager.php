<?php

namespace BasketPlanner\TeamBundle\Service;

use Doctrine\ORM\EntityManager;
use BasketPlanner\TeamBundle\Entity\TeamUser;
use BasketPlanner\TeamBundle\Entity\Team;
use BasketPlanner\UserBundle\Entity\User;
use BasketPlanner\TeamBundle\Entity\Invite;
use BasketPlanner\TeamBundle\Exception\TeamException;
use Symfony\Component\HttpFoundation\Response;

class TeamManager{

    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * leave team
     *
     * @var integer $userId id of the user
     * @var integer $teamId id of the team to leave
     * @throws TeamException
     */
    public function leaveTeam($userId, $teamId)
    {
        $userRole = $this->getUserRoleInTeam($userId, $teamId);
        //check if user is part of the team
        if ($userRole != null){
            //check if user is owner of the team
            if($userRole != 'Owner'){
                $teamUser = $this->entityManager->getRepository('BasketPlannerTeamBundle:TeamUser')->findOneBy(array(
                    'team' => $teamId,
                    'user' => $userId
                ));

                $this->entityManager->remove($teamUser);
                $this->entityManager->flush();
            }else{
                throw new TeamException("Komandos sąvininkas negali palikti komandos!");
            }
        }else{
            throw new TeamException("Jūs neturite priegos!");
        }
    }

    /**
     * delete team
     *
     * @var integer $userId id of the user
     * @var integer $teamId id of the team to remove
     * @throws TeamException
     */
    public function deleteTeam($userId, $teamId)
    {
        $userRole = $this->getUserRoleInTeam($userId, $teamId);
        //check if user is part of the team and is owner of the team
        if ($userRole == 'Owner')
        {
            //TO DO: notifications, check for active matches
            $team = $this->entityManager->getRepository('BasketPlannerTeamBundle:Team')->find($teamId);
            $teamPlayers = $team->getTeamUser();

            foreach ($teamPlayers as $player)
            {
                $this->entityManager->remove($player);
            }

            $this->entityManager->remove($team);
            $this->entityManager->flush();
        }else{
            throw new TeamException("Jūs neturite priegos!");
        }
    }

    /**
     * invite user to team
     *
     * @var integer $userId id of the user which will be invited
     * @var integer $inviterId id of the user who invites
     * @var integer $teamId id of the team to remove
     * @throws TeamException
     */
    public function inviteToTeam($userId, $inviterId, $teamId)
    {

        //check if user who invites have privileges to invite
        if ($this->getUserRoleInTeam($inviterId, $teamId) !== 'Owner') {
            throw new TeamException("Įvyko klaida! Jūs neturite teisių kviesti žaidėjų į šią komandą!");
        }

        //check if user is not a member of team
        if ($this->getUserRoleInTeam($userId, $teamId) !== null) {
            throw new TeamException("Įvyko klaida! Šis žaidėjas jau yra šioje komandoje!");
        }

        //check if team is not full
        if ($this->getTeamPlayersCount($teamId) >= $this->getTeamPlayersLimit($teamId)) {
            throw new TeamException("Įvyko klaida! Komanda jau pilna!");
        }

        $inviteExists = $this->entityManager->getRepository('BasketPlannerTeamBundle:Invite')->findOneBy(array(
            'team' => $teamId,
            'user' => $userId
        ));

        //check if invite to same user and team already exists
        if ($inviteExists === null)
        {
            $user = $this->entityManager->getRepository('BasketPlannerUserBundle:User')->find($userId);
            $team = $this->entityManager->getRepository('BasketPlannerTeamBundle:Team')->find($teamId);

            if ($user != null && $team != null)
            {
                $invite = $this->createTeamInvite($user, $team);

                $this->entityManager->persist($user);
                $this->entityManager->persist($team);
                $this->entityManager->persist($invite);
                $this->entityManager->flush();
            } else {
                throw new TeamException("Įvyko klaida!! Nepavyko rasti nurodytos komandos arba vartotojo!");
            }
        } else {
            throw new TeamException("Įvyko klaida! Šis vartotojas jau pakviestas į pasirinktą komandą!");
        }
    }

    /**
     * remove invite
     *
     * @var integer $userId id of the user which was invited
     * @var integer $inviteId id of the invite
     * @throws TeamException
     */
    public function inviteDelete($userId, $inviteId)
    {
        $invite = $this->entityManager->getRepository('BasketPlannerTeamBundle:Invite')->find($inviteId);

        //check if such invite exist
        if ($invite == null)
        {
            throw new TeamException("Įvyko klaida! Pakvietimo rasti nepavyko!");
        }

        $userRole = $this->getUserRoleInTeam($userId, $invite->getTeam()->getId());
        //check if user have privileges to remove invite
        if ($userRole == 'Owner') {
            $this->entityManager->remove($invite);
            $this->entityManager->flush();
        } else {
            throw new TeamException("Įvyko klaida! Jūs neturite prieigos pašalinti šį pakvietimą!");
        }
    }

    /**
     * accept invite
     *
     * @var integer $userId id of currently logged in user
     * @var integer $inviteId id of the invite
     * @throws TeamException
     */
    public function inviteAccept($userId, $inviteId)
    {
        $invite = $this->entityManager->getRepository('BasketPlannerTeamBundle:Invite')->find($inviteId);

        if($invite == null)
        {
            throw new TeamException("Įvyko klaida! Pakvietimo rasti nepavyko!");
        }

        //check if current user is user which have been invited
        if ($invite->getUser()->getId() === $userId) {
            throw new TeamException("Įvyko klaida! Jūs neturite prieigos priimti šį pakvietimą!");
        }

        $teamId = $invite->getTeam()->getId();
        $teamPlayers = $this->getTeamPlayersCount($teamId);
        //check if is not full
        if ($teamPlayers != null && $teamPlayers < $this->getTeamPlayersLimit($teamId))
        {
            $user = $this->entityManager->getRepository('BasketPlannerUserBundle:User')->find($userId);
            $team = $this->entityManager->getRepository('BasketPlannerTeamBundle:Team')->find($teamId);

            $teamUser = $this->createTeamPlayer($user, $team, 'Player');

            //TO DO: notificate team players about new user
            $this->entityManager->persist($teamUser);
            $this->entityManager->remove($invite);
            $this->entityManager->flush();
        } else {
            throw new TeamException("Įvyko klaida! Pasiektas komandos žaidėjų limitas!");
        }
    }

    /**
     * change status of the invite
     *
     * @var integer $userId id of currently logged in user
     * @var integer $inviteId id of the invite
     * @var string $inviteStatus new status of the invite
     * @throws TeamException
     */
    public function inviteChangeStatus($userId, $inviteId, $inviteStatus)
    {
        $invite = $this->entityManager->getRepository('BasketPlannerTeamBundle:Invite')->find($inviteId);

        if($invite == null)
        {
            throw new TeamException("Įvyko klaida! Pakvietimo rasti nepavyko!");
        }

        if($invite->getUser()->getId() != $userId)
        {
            throw new TeamException("Įvyko klaida! Jūs neturite priegos prie šio pakvietimo!");
        }

        if($inviteStatus == 'Seen'){
            $invite->setStatus('Seen');
        }else if ($inviteStatus == 'Rejected'){
            $invite->setStatus('Rejected');
        }

        //TO DO: notificate user about status change
        $this->entityManager->persist($invite);
        $this->entityManager->flush();

    }

    /**
     * get count of teams created or joined by user
     *
     * @var integer $user
     * @var string $role
     *
     * @return integer
     */
    public function getUserTeamsCount($user, $role)
    {

        $results = $this->entityManager->getRepository('BasketPlannerTeamBundle:Team')->getUserTeamsCount($user, $role);

        if ($results == null){
            return 0;
        }

        return $results;
    }

    /**
     * get count of teams players
     *
     * @var integer $team Team id
     *
     * @return integer
     */
    public function getTeamPlayersCount($team)
    {
        $results = $this->entityManager->getRepository('BasketPlannerTeamBundle:Team')->getTeamPlayersCount($team);

        if ($results == null){
            return 0;
        }

        return $results;
    }

    /**
     * get user role in team
     *
     * @var integer $user user id
     * @var integer $team team id
     *
     * @return string
     */
    public function getUserRoleInTeam($user, $team)
    {
        $results = $this->entityManager->getRepository('BasketPlannerTeamBundle:Team')->getUserRoleInTeam($user, $team);

        if (count($results) > 0) {
            $object = $results[0];
            $role = $object['role'];
        }else{
            return null;
        }

        return $role;
    }

    /**
     * get max team players
     *
     * @var integer $team Team id
     *
     * @return integer
     */
    public function getTeamPlayersLimit($team)
    {
        $teamEntity = $this->entityManager->getRepository('BasketPlannerTeamBundle:Team')->find($team);

        if ($teamEntity == null){
            return 0;
        }

        $type = $teamEntity->getType();
        $limit = $type->getPlayers()/ 2;

        return $limit;
    }

    /**
     * create teamUser object
     *
     * @var User $user
     * @var Team $team
     * @var string $role
     *
     * @return TeamUser
     */
    public function createTeamPlayer(User $user, Team $team, $role)
    {
        $teamPlayer = new TeamUser();
        $teamPlayer->setUser($user);
        $teamPlayer->setTeam($team);
        $teamPlayer->setRole($role);

        return $teamPlayer;
    }

    /**
     * create invite object
     *
     * @var User $user
     * @var Team $team
     *
     * @return Invite
     */
    public function createTeamInvite(User $user, Team $team)
    {
        $invite = new Invite();
        $invite->setStatus('New');
        $invite->setUser($user);
        $invite->setTeam($team);
        $invite->setCreated(new \DateTime('now'));

        return $invite;
    }

    /**
     * return possible team member roles
     *
     * @return array
     */
    public function getPossibleRoles()
    {
        return array(
            'Owner' => 'Sąvininkas',
            'Assistant' => 'Padėjėjas',
            'Player' => 'Žaidėjas'
        );
    }
    /**
     * create json response
     *
     * @var string $message message to define action state
     * @var string $status status variable to tell js functions about state
     * @var integer $statusCode response status code
     *
     * @return object
     */
    public function createJSonResponse($message, $status, $statusCode)
    {
        $responseBody = json_encode(array('message' => $message, 'status' => $status));
        $response = new Response($responseBody, $statusCode, array(
            'Content-Type' => 'application/json'
        ));

        return $response;
    }
}