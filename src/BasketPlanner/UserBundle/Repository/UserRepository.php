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
                SELECT u.id, u.profilePicture, u.firstName, u.lastName, u.email  FROM BasketPlannerUserBundle:User u
                WHERE CONCAT(u.firstName, u.lastName) LIKE :string
                OR u.email LIKE :string')
            ->setParameter('string','%'.$string.'%')
            ->getResult();

        return $users;
    }
}