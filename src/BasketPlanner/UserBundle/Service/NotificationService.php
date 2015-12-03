<?php

namespace BasketPlanner\UserBundle\Service;

use BasketPlanner\UserBundle\Entity\Notification;
use BasketPlanner\UserBundle\Entity\NotificationUser;
use BasketPlanner\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\Routing\RouterInterface;

class NotificationService {
    private $entityManager;
    private $emailProducer;
    private $notificationsProducer;
    private $router;

    public function __construct(EntityManager $entityManager,
                                ProducerInterface $emailProducer,
                                ProducerInterface $notificationsProducer,
                                RouterInterface $router){
        $this->entityManager = $entityManager;
        $this->emailProducer = $emailProducer;
        $this->notificationsProducer = $notificationsProducer;
        $this->router = $router;
    }


    /**
     * add notifications to users
     *
     * @var integer $matchId   joined match id
     * @var integer $userId   id of user who joined match
     * @var boolean $full   mathc full (true) or not (false)
     *
     */
    public function matchJoinNotification($matchId, $userId, $full){

        $url = $this->router->generate('basket_planner_match_show', ['id' => $matchId], true);

        if($full){
            $query = $this->entityManager
                ->createQuery('
                SELECT m.id, u.id, u.email, u.firstName, u.lastName  FROM BasketPlanner\MatchBundle\Entity\Match m
                INNER JOIN m.players u
                WHERE m.id = :matchId
                AND u.id <> :userId')
                ->setParameter('matchId', $matchId)
                ->setParameter('userId', $userId);
            $results = $query->getArrayResult();
            foreach ($results as $result) {
                $message = 'Sveiki ' . $result['firstName'] . ' ' . $result['lastName'] . ', mačas,
                     kuriame Jūs dalyvaujate, buvo surinktas.
                     Norėdami peržiūrėti prisijungusius žaidėjus ar kitą informaciją spauskite ant nuorodos:
                     <a href="'.$url.'">Mačo peržiūra</a>';
                $msg = array(
                    'email' => $result['email'],
                    'subject' => 'BasketPlanner - surinktas mačas.',
                    'message' => $message
                );
                $this->emailProducer->publish(serialize($msg), 'send_email');
            }
        }else{
            $query = $this->entityManager
                ->createQuery('
                SELECT u.id FROM BasketPlanner\MatchBundle\Entity\Match m
                INNER JOIN m.players u
                WHERE m.id = :matchId
                AND u.id <> :userId')
                ->setParameter('matchId', $matchId)
                ->setParameter('userId', $userId);
            $results = $query->getArrayResult();

            $user = $this->entityManager->getRepository('BasketPlannerUserBundle:User')->find($userId);
            $text = 'Naujas žaidėjas prisijungė prie mačo. Norėdami sužinoti detalesnę informaciją peržiūrėkite mačą.';
            $msg = array(
                'title' => $user->getFirstName().' prisijungė prie mačo!',
                'text' => $text,
                'link' => $url,
                'users' => $results
            );
            $this->notificationsProducer->publish(serialize($msg), 'notifications');
        }
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
