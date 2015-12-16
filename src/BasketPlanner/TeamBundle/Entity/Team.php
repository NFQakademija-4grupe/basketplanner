<?php

namespace BasketPlanner\TeamBundle\Entity;

use BasketPlanner\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity()
 */
class Team
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=80, unique = true)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="BasketPlanner\MatchBundle\Entity\Type")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     *
     * @Assert\NotBlank(
     *     message = "Pra�ome pasirinkti komandos tip?"
     * )
     * @Assert\Valid()
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * team users association
     *
     * @ORM\OneToMany(targetEntity="BasketPlanner\TeamBundle\Entity\TeamUser", mappedBy="team")
     */
    protected $teamUser;

    /**
     * team invite association
     *
     * @ORM\OneToMany(targetEntity="BasketPlanner\TeamBundle\Entity\Invite", mappedBy="team")
     */
    protected $teamInvite;

    public function __construct()
    {
        $this->teamUser = new ArrayCollection();
        $this->teamInvite = new ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     *
     * @return Team
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Team
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set created
     *
     * @param string $created
     *
     * @return Team
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

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Team
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add team member
     *
     * @param \BasketPlanner\TeamBundle\Entity\TeamUser $teamUser
     * @return Team
     */
    public function addTeamUser($teamUser)
    {
        $this->teamUser[] = $teamUser;

        return $this;
    }

    /**
     * Remove team member
     *
     * @param \BasketPlanner\TeamBundle\Entity\TeamUser $teamUser
     */
    public function removeTeamUser($teamUser)
    {
        $this->teamUser->removeElement($teamUser);
    }

    /**
     * Get team members
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTeamUser()
    {
        return $this->teamUser;
    }

    /**
     * Add team invite
     *
     * @param \BasketPlanner\TeamBundle\Entity\Invite $invite
     * @return Team
     */
    public function addTeamInvite($teamInvite)
    {
        $this->teamInvite[] = $teamInvite;

        return $this;
    }

    /**
     * Remove team invite
     *
     * @param \BasketPlanner\TeamBundle\Entity\Invite $teamInvite
     */
    public function removeTeamInvite($teamInvite)
    {
        $this->teamInvite->removeElement($teamInvite);
    }

    /**
     * Get team invites
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTeamInvite()
    {
        return $this->teamInvite;
    }
}
