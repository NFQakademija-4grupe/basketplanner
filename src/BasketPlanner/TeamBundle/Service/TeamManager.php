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
        $query = $this->entityManager
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

        return $query->getArrayResult();
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
        $query = $this->entityManager
            ->createQuery('
                SELECT team, IDENTITY(tu.user), tu.role, t FROM BasketPlanner\TeamBundle\Entity\Team team
                LEFT JOIN team.type t
                INNER JOIN BasketPlanner\TeamBundle\Entity\TeamUser tu
                WITH team.id=tu.team
                WHERE tu.user = :userId
                AND tu.role = :role
                GROUP BY tu.team
                ')
            ->setParameter('userId', $user)
            ->setParameter('role', $role);

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
        $query = $this->entityManager
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
        $query = $this->entityManager
            ->createQueryBuilder()
            ->select('tu.role')
            ->from('BasketPlannerTeamBundle:Team','t')
            ->leftJoin('t.teamUser','tu')
            ->where('t.id = ?1')
            ->andWhere('tu.user = ?2')
            ->setParameter(1, $team)
            ->setParameter(2, $user);
        $results = $query->getQuery()->getArrayResult();
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
        $query = $this->entityManager->createQuery("
            SELECT u.id, u.firstName, u.lastName, u.profilePicture, tp.role
                FROM BasketPlannerTeamBundle:TeamUser tp
                JOIN BasketPlannerUserBundle:User u
                WITH tp.user = u.id
                WHERE tp.team = :team")
            ->setParameter('team', $team);

        return $query->getArrayResult();
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
        $query = $this->entityManager
            ->createQueryBuilder()
            ->select('inv.id as inviteId, inv.status, inv.created, t.name, u.id as userId, u.firstName, u.lastName')
            ->from('BasketPlannerTeamBundle:Invite','inv')
            ->leftJoin('inv.team','t')
            ->leftJoin('inv.user','u')
            ->leftJoin('t.teamUser','tu')
            ->where('tu.user = ?1')
            ->andWhere('tu.role = ?2')
            ->setParameter(1, $user)
            ->setParameter(2, 'Owner');

        return $query->getQuery()->getArrayResult();
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
        $query = $this->entityManager
            ->createQueryBuilder()
            ->select('inv.id as inviteId, inv.status, inv.created, t.id as teamId, t.name, u.id as userId, u.firstName, u.lastName')
            ->from('BasketPlannerTeamBundle:Invite','inv')
            ->leftJoin('inv.team','t')
            ->leftJoin('t.teamUser','tu')
            ->leftJoin('tu.user','u')
            ->where('inv.user = ?1')
            ->andWhere('tu.role = ?2')
            ->andWhere('inv.status <> ?3')
            ->setParameter(1, $user)
            ->setParameter(2, 'Owner')
            ->setParameter(3, 'Rejected');

        return $query->getQuery()->getArrayResult();
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