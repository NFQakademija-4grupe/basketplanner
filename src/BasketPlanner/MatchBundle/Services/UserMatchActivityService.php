<?php

namespace BasketPlanner\MatchBundle\Services;

use BasketPlanner\MatchBundle\Entity\Match;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class UserMatchActivityService{

    private $entityManager;

    public function __construct(EntityManager $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * load matches created by user into array
     *
     * @var integer $userId   tells which user activity to load
     * @var integer $limit   tells how many record to load
     *
     * @return array
     */
    public function loadUserCreatedMatches($userId, $limit){
        $results = $this->entityManager->getRepository('BasketPlannerMatchBundle:Match')->findBy(
                array('owner' => $userId),
                array(),
                $limit);

        return $results;
    }

    /**
     * load matches attended, but not created by user into array
     *
     * @var integer $userId   tells which user activity to load
     * @var integer $limit   tells how many record to load
     *
     * @return array
     */
    public function loadUserAttendedMatches($user, $limit){
        $query = $this->entityManager
            ->createQuery('
                SELECT m, t, c  FROM BasketPlanner\MatchBundle\Entity\Match m
                INNER JOIN m.players u
                    WITH u = :user
                LEFT JOIN m.type t
                LEFT JOIN m.court c
                WHERE m.owner <> :userId')
            ->setParameter('user', $user)
            ->setParameter('userId', $user->getId());

        $results = $query->getArrayResult();

        return $results;
    }

}