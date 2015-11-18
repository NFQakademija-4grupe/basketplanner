<?php

namespace BasketPlanner\TeamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use BasketPlanner\UserBundle\Entity\User;

/**
 * UserTeam
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class UserTeam
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
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="BasketPlanner\UserBundle\Entity\User", inversedBy="id", cascade={"all"})
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="BasketPlanner\TeamBundle\Entity\Team", inversedBy="id", cascade={"all"})
     *
     */
    private $team;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=15)
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
     * Set userId
     *
     * @param integer $userId
     *
     * @return UserTeam
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set teamId
     *
     * @param integer $teamId
     *
     * @return UserTeam
     */
    public function setTeamId($teamId)
    {
        $this->teamId = $teamId;

        return $this;
    }

    /**
     * Get teamId
     *
     * @return integer
     */
    public function getTeamId()
    {
        return $this->teamId;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return UserTeam
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
     *
     * @return UserTeam
     */
    public function setUser(\BasketPlanner\UserBundle\Entity\User $user = null)
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
     *
     * @return UserTeam
     */
    public function setTeam(\BasketPlanner\TeamBundle\Entity\Team $team = null)
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
