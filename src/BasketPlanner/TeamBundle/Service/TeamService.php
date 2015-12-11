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
                SELECT tu, team, t  FROM BasketPlanner\TeamBundle\Entity\Team team
                LEFT JOIN team.teamUser tu
                LEFT JOIN team.type t
                WHERE tu.user = :userId')
            ->setParameter('userId', $user);
        $teams = $query->getArrayResult();

        return $teams;
    }

    public function createTeamPlayer(User $user, Team $team, $role){
        $teamPlayer = new TeamUser();
        $teamPlayer->setUser($user);
        $teamPlayer->setTeam($team);
        $teamPlayer->setRole($role);

        return $teamPlayer;

    }

    public function getPossibleRoles(){
        return array('Owner', 'Assistant', 'Player');
    }
}