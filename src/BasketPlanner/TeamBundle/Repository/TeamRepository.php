<?php

namespace BasketPlanner\TeamBundle\Repository;

use Doctrine\ORM\EntityRepository;
use BasketPlanner\TeamBundle\Entity\TeamUser;
use BasketPlanner\TeamBundle\Entity\Team;
use BasketPlanner\UserBundle\Entity\User;

class TeamRepository extends EntityRepository
{

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
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(t.team)')
            ->from('BasketPlannerTeamBundle:TeamUser','t')
            ->where('t.user = ?1')
            ->andWhere('t.role = ?2')
            ->setParameter(1, $user)
            ->setParameter(2, $role);

        return $query->getQuery()->getSingleScalarResult();
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
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT team, IDENTITY(tu.user), tu.role, t FROM BasketPlanner\TeamBundle\Entity\Team team
                LEFT JOIN team.type t
                INNER JOIN BasketPlanner\TeamBundle\Entity\TeamUser tu
                WITH team.id=tu.team
                WHERE tu.user = :userId
                GROUP BY tu.team
                ')
            ->setParameter('userId', $user);

        return $query->getArrayResult();
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
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(t.user)')
            ->from('BasketPlannerTeamBundle:TeamUser','t')
            ->where('t.team = ?1')
            ->setParameter(1, $team);

        return $query->getQuery()->getSingleScalarResult();
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
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('tu.role')
            ->from('BasketPlannerTeamBundle:Team','t')
            ->leftJoin('t.teamUser','tu')
            ->where('t.id = ?1')
            ->andWhere('tu.user = ?2')
            ->setParameter(1, $team)
            ->setParameter(2, $user);
        $results = $query->getQuery()->getArrayResult();

        return $results;
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
        $query = $this->getEntityManager()->createQuery("
            SELECT u.id, u.firstName, u.lastName, u.profilePicture, tp.role
                FROM BasketPlannerTeamBundle:TeamUser tp
                JOIN BasketPlannerUserBundle:User u
                WITH tp.user = u.id
                WHERE tp.team = :team")
            ->setParameter('team', $team);

        return $query->getArrayResult();
    }

}