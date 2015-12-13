<?php

namespace BasketPlanner\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use BasketPlanner\UserBundle\Entity\User;

class UserRepository extends EntityRepository
{

    public function findByLetters($string)
    {

        return $this->getEntityManager()->createQuery('SELECT u FROM BasketPlannerUserBundle:User u
                WHERE u.firstname LIKE :string OR u.lastname LIKE :string OR u.email LIKE :string')
            ->setParameter('string','%'.$string.'%')
            ->getResult();

    }
}