<?php

namespace BasketPlanner\TeamBundle\Repository;

use Doctrine\ORM\EntityRepository;
use BasketPlanner\TeamBundle\Entity\Invite;

class InviteRepository extends EntityRepository
{

    /**
     * get created invites
     *
     * @var integer $user user id
     *
     * @return array
     */
    public function getUserCreatedInvites($user)
    {
        $query = $this->getEntityManager()
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
        $query = $this->getEntityManager()
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

}