<?php

namespace BasketPlanner\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use BasketPlanner\UserBundle\Entity\User;

class UserRepository extends EntityRepository
{

    public function findByLetters($string)
    {

        $users = $this->getEntityManager()
            ->createQuery('
                SELECT u.id, u.profilePicture, CONCAT(CONCAT(u.firstName, :space), u.lastName) as fullName, u.email  FROM BasketPlannerUserBundle:User u
                WHERE CONCAT(CONCAT(u.firstName, :space), u.lastName) LIKE :string
                OR u.email LIKE :string')
            ->setParameter('string','%'.$string.'%')
            ->setParameter('space',' ');
        $results = $users->getResult();

        return $results;
    }
}