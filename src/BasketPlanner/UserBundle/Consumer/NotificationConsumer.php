<?php

namespace BasketPlanner\UserBundle\Consumer;

use BasketPlanner\UserBundle\Entity\Notification;
use BasketPlanner\UserBundle\Entity\NotificationUser;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;

class NotificationConsumer implements ConsumerInterface
{
    private $entityManager;
    private $logger;

    public function __construct(EntityManager $entityManager, LoggerInterface $logger){
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }
    public function execute(AMQPMessage $msg)
    {
        $data = unserialize($msg->body);
        $title = $data['title'];
        $text = $data['text'];
        $link = $data['link'];
        $users = $data['users'];

        $notification = new Notification();
        $notification->setTitle($title);
        $notification->setText($text);
        $notification->setLink($link);
        $notification->setDate(new \DateTime('now'));
        $this->entityManager->persist($notification);

        foreach($users as $user) {
            $this->logger->info('Got userID: '.$user['id']);
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
}