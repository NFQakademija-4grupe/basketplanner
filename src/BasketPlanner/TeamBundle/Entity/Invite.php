<?php

namespace BasketPlanner\TeamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use BasketPlanner\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Invite
 *
 * @ORM\Table(name="team_invite")
 * @ORM\Entity(repositoryClass="BasketPlanner\TeamBundle\Repository\InviteRepository")
 */
class Invite
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
     * @ORM\ManyToOne(targetEntity="BasketPlanner\UserBundle\Entity\User", inversedBy="teamInvite")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Pasirinkite vartotoją.")
     */
    private $user;

    /**
     *
     * @ORM\ManyToOne(targetEntity="BasketPlanner\TeamBundle\Entity\Team", inversedBy="teamInvite")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Pasirinkite komandą.")
     *
     */
    private $team;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", columnDefinition="enum('New', 'Seen', 'Accepted', 'Rejected')")
     */
    private $status = 'New';

    /**
     * @var string
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

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
     * Set status
     *
     * @param string $status
     *
     * @return Invite
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set user
     *
     * @param \BasketPlanner\UserBundle\Entity\User $user
     * @return Invite
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
     * @return Invite
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

    /**
     * Set created
     *
     * @param string $created
     *
     * @return Invite
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

}
