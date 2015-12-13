<?php

namespace BasketPlanner\TeamBundle\Manager;

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
        $query = $this->entityManager
            ->createQueryBuilder()
            ->select('COUNT(t.team)')
            ->from('BasketPlannerTeamBundle:TeamUser','t')
            ->where('t.user = ?1')
            ->andWhere('t.role = ?2')
            ->setParameter(1, $user)
            ->setParameter(2, $role);
        $count = $query->getQuery()->getSingleScalarResult();

        return $count;
    }

    /**
     * get teams created and joined by user
     *
     * @var integer $user
     * @var string $role
     *
     * @return array
     */
    public function getUserTeams($user)
    {
        $query = $this->entityManager
            ->createQuery('
                SELECT team, IDENTITY(tu.user), tu.role, t FROM BasketPlanner\TeamBundle\Entity\Team team
                LEFT JOIN team.type t
                INNER JOIN BasketPlanner\TeamBundle\Entity\TeamUser tu
                WITH team.id=tu.team
                WHERE tu.user = :userId
                GROUP BY tu.team
                ')
            ->setParameter('userId', $user);
        $teams = $query->getArrayResult();

        return $teams;
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
        $query = $this->entityManager
            ->createQueryBuilder()
            ->select('COUNT(t.user)')
            ->from('BasketPlannerTeamBundle:TeamUser','t')
            ->where('t.team = ?1')
            ->setParameter(1, $team);
        $count = $query->getQuery()->getSingleScalarResult();

        return $count;
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
        $query = $this->entityManager->createQuery("
            SELECT u.id, u.firstName, u.lastName, u.profilePicture, tp.role
                FROM BasketPlannerTeamBundle:TeamUser tp
                JOIN BasketPlannerUserBundle:User u
                WITH tp.user = u.id
                WHERE tp.team = :team")
            ->setParameter('team', $team);
        $teams = $query->getArrayResult();

        return $teams;
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