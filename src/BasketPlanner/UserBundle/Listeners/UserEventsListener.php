<?php

namespace BasketPlanner\UserBundle\Listeners;

use BasketPlanner\UserBundle\Events\RegistrationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserEventsListener implements EventSubscriberInterface
{
    protected $twig;
    protected $mailer;

    public function __construct(\Twig_Environment $twig, \Swift_Mailer $mailer)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'registration.event' => array(
                array('onRegistrationEvent', 10)
            )
        );
    }

    public function onRegistrationEvent(RegistrationEvent $event)
    {
        $user = $event->getUser();
        $message = \Swift_Message::newInstance()
            ->setSubject('Hello ' . $user->getFirstName() . '!')
            ->setFrom('noreply@basketplanner.lt')
            ->setTo($user->getEmail())
            ->setBody('Welcome to our site.')
        ;
        $this->mailer->send($message);
    }
}