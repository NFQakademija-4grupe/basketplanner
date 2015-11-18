<?php

namespace BasketPlanner\TeamBundle\Events;

use Symfony\Component\EventDispatcher\Event;
use BasketPlanner\UserBundle\Entity\User;
use BasketPlanner\TeamBundle\Entity\Team;

class InviteEvent extends Event
{
    protected $user;
    protected $team;

    public function __construct(User $user, Team $team)
    {
        $this->user = $user;
        $this->team = $team;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getTeam()
    {
        return $this->team;
    }
}