<?php

namespace BasketPlanner\TeamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use BasketPlanner\UserBundle\Entity\User;

/**
 * TeamUsers
 *
 * @ORM\Table(name="team_user")
 * @ORM\Entity
 */
class TeamUser
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
     * @ORM\ManyToOne(targetEntity="BasketPlanner\UserBundle\Entity\User", inversedBy="teamUser")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     */
    private $user;

    /**
     *
     * @ORM\ManyToOne(targetEntity="BasketPlanner\TeamBundle\Entity\Team", inversedBy="teamUser")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     *
     */
    private $team;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", columnDefinition="enum('Owner', 'Assistant', 'Player')")
     */
    private $role;


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
     * Set role
     *
     * @param string $role
     *
     * @return TeamUser
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set user
     *
     * @param \BasketPlanner\UserBundle\Entity\User $user
     * @return TeamUser
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
     * Set team
     *
     * @param \BasketPlanner\TeamBundle\Entity\Team $team
     * @return TeamUser
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
