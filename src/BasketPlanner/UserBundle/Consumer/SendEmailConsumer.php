<?php

namespace BasketPlanner\UserBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class SendEmailConsumer implements ConsumerInterface
{
    private $mailer;
    private $logger;

    public function __construct(\Swift_Mailer $mailer, LoggerInterface $logger){
        $this->mailer = $mailer;
        $this->logger = $logger;
    }
    public function execute(AMQPMessage $msg)
    {
        $data = unserialize($msg->body);
        $email = $data['email'];
        $subject = $data['subject'];
        $message = $data['message'];

        $mail = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array('noreply@basketplanner.lt' => 'BasketPlanner'))
            ->setTo($email)
            ->setBody($message);

        $delivered = $this->mailer->send($mail);

        if(!$delivered){
            return false;
        }
    }
}