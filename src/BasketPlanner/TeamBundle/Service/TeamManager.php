<?php

namespace BasketPlanner\TeamBundle\Service;

use Doctrine\ORM\EntityManager;
use BasketPlanner\TeamBundle\Entity\TeamUser;
use BasketPlanner\TeamBundle\Entity\Team;
use BasketPlanner\UserBundle\Entity\User;

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
        $results = $this->entityManager->getRepository('BasketPlannerTeamBundle:Team')->getUserTeamsCount($user, $role);

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
        $results = $this->entityManager->getRepository('BasketPlannerTeamBundle:Team')->getTeamPlayersCount($team);

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
        $results = $this->entityManager->getRepository('BasketPlannerTeamBundle:Team')->getTeamPlayers($team);

        return $results;
    }

    /**
     * get max team players
     *
     * @var integer $teamId Team id
     *
     * @return integer
     */
    public function getTeamPlayersLimit($teamId)
    {
        $team = $this->entityManager->getRepository('BasketPlannerTeamBundle:Team')->find($teamId);
        $type = $team->getType();
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
     * get created invites
     *
     * @var integer $user user id
     *
     * @return array
     */
    public function getUserCreatedInvites($user)
    {
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
}