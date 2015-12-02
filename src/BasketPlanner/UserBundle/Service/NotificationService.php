<?php

namespace BasketPlanner\UserBundle\Service;

use BasketPlanner\UserBundle\Entity\Notification;
use BasketPlanner\UserBundle\Entity\NotificationUser;
use BasketPlanner\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class NotificationService {
    private $entityManager;

    public function __construct(EntityManager $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * add notifications to users
     *
     * @var Notification $notification   notification object
     * @var array $users   array of users who should get the notification
     *
     */
    public function addNotification(Notification $notification,array $users){

        foreach ($users as $user) {
            $userNotification = new NotificationUser();
            $userNotification->setUser($user);
            $userNotification->setNotification($notification);

            $this->entityManager->persist($user);
            $this->entityManager->persist($userNotification);
        }

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }

    /**
     * get notifications dedicated to user
     *
     * @var User $user
     * @var boolean $seen   tells to get only unseen (false) or all (true) notifications
     *
     * @return array
     */
    public function getNotifications($user, $seen){

    }

    /**
     * get unread notifications count dedicated to user
     *
     * @var User $user
     *
     * @return integer
     */
    public function getUnreadNotificationsCount($user){

    }
}
