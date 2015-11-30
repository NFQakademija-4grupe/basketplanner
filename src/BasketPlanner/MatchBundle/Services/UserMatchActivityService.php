<?php

namespace BasketPlanner\MatchBundle\Services;

use BasketPlanner\MatchBundle\Entity\Match;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class UserMatchActivityService{

    private $entityManager;
    private $logger;

    public function __construct(EntityManager $entityManager, LoggerInterface $logger = null){
        $this->entityManager = $entityManager;
        $this->logger = $logger;
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
            if($this->logger != null){
                $this->logger->error('An error occurred while trying to load user activity: '.$e->getMessage());
            }
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
                    SELECT m, t, c  FROM BasketPlanner\MatchBundle\Entity\Match m
                    INNER JOIN m.players u
                        WITH u = :user
                    LEFT JOIN m.type t
                    LEFT JOIN m.court c
                    WHERE m.owner <> :userId')
                ->setParameter('user', $user)
                ->setParameter('userId', $user->getId());

            $results = $query->getArrayResult();
        }catch ( \Exception  $e){
            if($this->logger != null){
                $this->logger->error('An error occurred while trying to load user activity: '.$e->getMessage());
            }
        }

        return $results;
    }

}