<?php

namespace BasketPlanner\UserBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Exception\AMQPRuntimeException;
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
        try{
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