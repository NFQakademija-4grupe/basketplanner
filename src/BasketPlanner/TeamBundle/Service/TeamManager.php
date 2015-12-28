<?php

namespace BasketPlanner\TeamBundle\Service;

use Doctrine\ORM\EntityManager;
use BasketPlanner\TeamBundle\Entity\TeamUser;
use BasketPlanner\TeamBundle\Entity\Team;
use BasketPlanner\UserBundle\Entity\User;
use BasketPlanner\TeamBundle\Entity\Invite;
use Symfony\Component\HttpFoundation\Response;

class TeamManager{

    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
        if (!is_integer($user) || !is_integer($team))
        {
            return;
        }

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