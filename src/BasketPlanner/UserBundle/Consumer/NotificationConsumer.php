<?php

namespace BasketPlanner\UserBundle\Consumer;

use BasketPlanner\UserBundle\Entity\Notification;
use BasketPlanner\UserBundle\Entity\NotificationUser;
use BasketPlanner\UserBundle\Service\NotificationService;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Exception\AMQPRuntimeException;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;

class NotificationConsumer implements ConsumerInterface
{
    private $entityManager;
    private $logger;
    private $notificationService;

    public function __construct(EntityManager $entityManager, LoggerInterface $logger,NotificationService $notificationService){
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->notificationService = $notificationService;
    }
    public function execute(AMQPMessage $msg)
    {
        try{
            $data = unserialize($msg->body);
            $title = $data['title'];
            $text = $data['text'];
            $link = $data['link'];
            $users = $data['users'];

            $this->notificationService->createNotification($title, $text, $link, $users);

            return ConsumerInterface::MSG_ACK;

        } catch (AMQPTimeoutException $te) {
            // nothing to read
        } catch (AMQPRuntimeException $re) {
            $err = error_get_last();

            if (stripos($err['message'], 'Interrupted system call') === false) {
                $this->logger->info('io error: ' . $err['message']);
            }
        }
    }
}