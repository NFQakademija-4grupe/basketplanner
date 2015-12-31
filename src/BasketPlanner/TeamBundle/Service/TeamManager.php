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
     * get count of teams created or joined by user
     *
     * @var integer $user
     * @var string $role
     *
     * @return integer
     */
    public function getUserTeamsCount($user, $role)
    {
        if (!is_integer($user) || !is_string($role))
        {
            return;
        }

        $results = $this->entityManager->getRepository('BasketPlannerTeamBundle:Team')->getUserTeamsCount($user, $role);

        if ($results == null){
            return 0;
        }

        return $results;
    }

    /**
     * get teams created and joined by user
     *
     * @var integer $user
     *
     * @return array
     */
    public function getUserTeams($user)
    {
        if (!is_integer($user))
        {
            return;
        }

        $results = $this->entityManager->getRepository('BasketPlannerTeamBundle:Team')->getUserTeams($user);

        return $results;
    }

    /**
     * get teams created and joined by user
     *
     * @var integer $user
     * @var string $role
     *
     * @return array
     */
    public function getUserTeamsByRole($user, $role)
    {
        if (!is_integer($user) || !is_string($role))
        {
            return;
        }

        $results = $this->entityManager->getRepository('BasketPlannerTeamBundle:Team')->getUserTeamsByRole($user, $role);

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
        if (!is_integer($team))
        {
            return;
        }

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
     * get list of teams players
     *
     * @var integer $team Team id
     *
     * @return array
     */
    public function getTeamPlayers($team)
    {

        if (!is_integer($team))
        {
            return;
        }

        $results = $this->entityManager->getRepository('BasketPlannerTeamBundle:Team')->getTeamPlayers($team);

        return $results;
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
        if (!is_integer($team))
        {
            return;
        }

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
     * get created invites
     *
     * @var integer $user user id
     *
     * @return array
     */
    public function getUserCreatedInvites($user)
    {
        if (!is_integer($user))
        {
            return;
        }

        $results = $this->entityManager->getRepository('BasketPlannerTeamBundle:Invite')->getUserCreatedInvites($user);

        return $results;
    }

    /**
     * get received invites
     *
     * @var integer $user user id
     *
     * @return array
     */
    public function getUserReceivedInvites($user)
    {
        if (!is_integer($user))
        {
            return;
        }

        $results = $this->entityManager->getRepository('BasketPlannerTeamBundle:Invite')->getUserReceivedInvites($user);

        return $results;
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