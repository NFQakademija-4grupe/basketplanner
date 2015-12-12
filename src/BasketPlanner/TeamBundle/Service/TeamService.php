<?php

namespace BasketPlanner\TeamBundle\Service;

use Doctrine\ORM\EntityManager;
use BasketPlanner\TeamBundle\Entity\TeamUser;
use BasketPlanner\TeamBundle\Entity\Team;
use BasketPlanner\UserBundle\Entity\User;

class TeamService{

    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * get count of teams created by user
     *
     * @var integer $user
     *
     * @return integer
     */
    public function getUserCreatedTeamsCount($user){

        $query = $this->entityManager
            ->createQueryBuilder()
            ->select('COUNT(t.team)')
            ->from('BasketPlannerTeamBundle:TeamUser','t')
            ->where('t.user = ?1')
            ->andWhere('t.role = ?2')
            ->setParameter(1, $user)
            ->setParameter(2, 'Owner');
        $count = $query->getQuery()->getSingleScalarResult();

        return $count;
    }

    /**
     * get teams created by user
     *
     * @var integer $user
     * @var string $role
     *
     * @return array
     */
    public function getUserTeams($user){

        $query = $this->entityManager
            ->createQuery('
                SELECT team, IDENTITY(tu.user), tu.role, COUNT(tu.team) as playersCount, t FROM BasketPlanner\TeamBundle\Entity\Team team
                LEFT JOIN team.teamUser tu
                LEFT JOIN team.type t
                GROUP BY tu.team
                HAVING tu.user = :userId')
            ->setParameter('userId', $user);
        $teams = $query->getArrayResult();

        return $teams;
    }

    /**
     * get count of teams members
     *
     * @var integer $team Team id
     *
     * @return integer
     */
    public function getTeamMembersCount($user){

        $query = $this->entityManager
            ->createQueryBuilder()
            ->select('IDENTITY(t.user) as user,IDENTITY(t.team) as team, COUNT(t.team) as playersCount')
            ->from('BasketPlannerTeamBundle:TeamUser','t')
            ->groupby('t.team')
            ->having('t.user = ?1')
            ->setParameter(1, $user);
        $count = $query->getQuery()->getArrayResult();

        return $count;
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
    public function createTeamPlayer(User $user, Team $team, $role){
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
    public function getPossibleRoles(){
        return array(
            'Owner' => 'Sąvininkas',
            'Assistant' => 'Padėjėjas',
            'Player' => 'Žaidėjas'
        );
    }
}