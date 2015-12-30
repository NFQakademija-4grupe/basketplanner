<?php

namespace BasketPlanner\MatchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use BasketPlanner\UserBundle\Entity\User;

/**
 * MatchUser
 *
 * @ORM\Table(name="match_user")
 * @ORM\Entity
 */
class MatchUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="BasketPlanner\UserBundle\Entity\User", inversedBy="joinedMatches")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     */
    private $user;

    /**
     *
     * @ORM\ManyToOne(targetEntity="BasketPlanner\MatchBundle\Entity\Match", inversedBy="players")
     * @ORM\JoinColumn(name="match_id", referencedColumnName="id")
     *
     */
    private $match;

    /**
     *
     * @ORM\ManyToOne(targetEntity="BasketPlanner\TeamBundle\Entity\Team", inversedBy="matchUser")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     *
     */
    private $team;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \BasketPlanner\UserBundle\Entity\User $user
     * @return MatchUser
     */
    public function setUser($user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \BasketPlanner\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set match
     *
     * @param \BasketPlanner\MatchBundle\Entity\Match $match
     * @return MatchUser
     */
    public function setMatch($match = null)
    {
        $this->match = $match;

        return $this;
    }

    /**
     * Get match
     *
     * @return \BasketPlanner\MatchBundle\Entity\Match
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * Set team
     *
     * @param \BasketPlanner\TeamBundle\Entity\Team $team
     * @return MatchUser
     */
    public function setTeam($team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return \BasketPlanner\TeamBundle\Entity\Team
     */
    public function getTeam()
    {
        return $this->team;
    }

}
