<?php

namespace BasketPlanner\UserBundle\Service;

use BasketPlanner\UserBundle\Entity\Notification;
use BasketPlanner\UserBundle\Entity\NotificationUser;
use BasketPlanner\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\Routing\RouterInterface;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Exception\AMQPRuntimeException;

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
                INNER JOIN m.players mu
                LEFT JOIN mu.user u
                WHERE m.id = :matchId
                AND u.id <> :userId')
                ->setParameter('matchId', $matchId)
                ->setParameter('userId', $userId);
            $results = $query->getArrayResult();

            $subject = 'BasketPlanner - surinktas mačas.';
            foreach ($results as $result) {
                $message = 'Sveiki ' . $result['firstName'] . ' ' . $result['lastName'] . ', mačas,
                     kuriame Jūs dalyvaujate, buvo surinktas.
                     Norėdami peržiūrėti prisijungusius žaidėjus ar kitą informaciją spauskite ant nuorodos:
                     <a href="'.$url.'">Mačo peržiūra</a>';
                $this->sendNotificationMQ($result['email'], $subject, $message);
            }

        }else{
            $query = $this->entityManager
                ->createQuery('
                    SELECT u.id FROM BasketPlanner\MatchBundle\Entity\Match m
                    INNER JOIN m.players mu
                    LEFT JOIN mu.user u
                    WHERE m.id = :matchId
                    AND u.id <> :userId
                ')
                ->setParameter('matchId', $matchId)
                ->setParameter('userId', $userId);
            $users = $query->getArrayResult();

            $user = $this->entityManager->getRepository('BasketPlannerUserBundle:User')->find($userId);
            $text = 'Naujas žaidėjas prisijungė prie mačo. Norėdami sužinoti detalesnę informaciją peržiūrėkite mačą.';
            $title = $user->getFirstName().' prisijungė prie mačo!';
            $this->createNotificationMQ($title, $text, $url, $users);
        }

    }

    /**
     * create notification
     *
     * @var string $title
     * @var string $text
     * @var string $url
     * @var array $users
     *
     */
    public function createNotification($title, $text, $url, $users){
        $notification = new Notification();
        $notification->setTitle($title);
        $notification->setText($text);
        $notification->setLink($url);
        $notification->setDate(new \DateTime('now'));
        $this->entityManager->persist($notification);

        foreach($users as $user) {
            $user = $this->entityManager->getRepository('BasketPlannerUserBundle:User')->find($user['id']);
            $notificationUser = new NotificationUser();
            $notificationUser->setUser($user);
            $notificationUser->setNotification($notification);
            $notificationUser->setSeen(false);
            $this->entityManager->persist($user);
            $this->entityManager->persist($notificationUser);
        }
        $this->entityManager->flush();
    }

    /**
     * create notification with message queue
     *
     * @var string $title
     * @var string $text
     * @var string $url
     * @var array $users
     *
     */
    public function createNotificationMQ($title, $text, $url, $users){
        $msg = array(
            'title' => $title,
            'text' => $text,
            'link' => $url,
            'users' => $users
        );
        try {
            $this->notificationsProducer->publish(serialize($msg), 'notifications');
        }catch (\Exception  $e){
            // if rabbitmq server is down ignore messanger
        }
    }

    /**
     * send notification with message queue
     *
     * @var string $email
     * @var string $subject
     * @var string $message
     *
     */
    public function sendNotificationMQ($email, $subject, $message){
        $msg = array(
            'email' => $email,
            'subject' => $subject,
            'message' => $message
        );
        try {
            $this->emailProducer->publish(serialize($msg), 'send_email');
        }catch (\Exception  $e){
            // if rabbitmq server is down ignore messanger
        }
    }


    /**
     * get unread notifications count dedicated to user
     *
     * @var integer $user
     *
     * @return integer
     */
    public function getUnreadNotificationsCount($user){

        $query = $this->entityManager
            ->createQueryBuilder()
            ->select('COUNT(n.notification)')
            ->from('BasketPlannerUserBundle:NotificationUser','n')
            ->where('n.user = ?1')
            ->andWhere('n.seen = false')
            ->setParameter(1, $user);
        $count = $query->getQuery()->getSingleScalarResult();

        return $count;
    }
}
