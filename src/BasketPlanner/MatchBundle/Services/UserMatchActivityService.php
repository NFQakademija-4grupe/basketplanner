<?php

namespace BasketPlanner\MatchBundle\Services;

use BasketPlanner\MatchBundle\Entity\Match;
use Doctrine\ORM\EntityManager;

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
        try{
            $results = $this->entityManager->getRepository('BasketPlannerMatchBundle:Match')->findBy(
                array('owner' => $userId),
                array(),
                $limit);
        }catch ( \Exception  $e){
            die(var_dump('adsad'));
        }

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
        try{
            $query = $this->entityManager
                ->createQuery('
                    SELECT m, t  FROM BasketPlanner\MatchBundle\Entity\Match m
                    INNER JOIN m.players u
                        WITH u = :user
                    LEFT JOIN m.type t
                    WHERE m.owner <> :userId')
                ->setParameter('user', $user)
                ->setParameter('userId', $user->getId());

            $results = $query->getArrayResult();
        }catch ( \Exception  $e){
            die(var_dump('failed'));
        }

        return $results;
    }

}