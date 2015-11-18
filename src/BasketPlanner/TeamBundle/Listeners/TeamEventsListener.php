<?php

namespace BasketPlanner\TeamBundle\Listeners;

use BasketPlanner\TeamBundle\Events\InviteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TeamEventsListener implements EventSubscriberInterface
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
            'invite.event' => array(
                array('onInviteEvent', 10)
            )
        );
    }

    public function onInviteEvent(InviteEvent $event)
    {
        //asd
    }
}