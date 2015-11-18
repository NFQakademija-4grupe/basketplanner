<?php

namespace BasketPlanner\MatchBundle\Entity;

use BasketPlanner\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="matches")
 */
class Match
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="\BasketPlanner\UserBundle\Entity\User", inversedBy="createdMatches")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     **/
    protected $owner;

    /**
     * @ORM\ManyToMany(targetEntity="\BasketPlanner\UserBundle\Entity\User", inversedBy="joinedMatches")
     * @ORM\JoinTable(name="matches_users")
     */
    protected $players;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="match")
     */
    protected $comments;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(
     *     message = "Aprašymas negali būti tuščias"
     * )
     * @Assert\Length(
     *     min = 10,
     *     max = 255,
     *     minMessage = "Aprašymas negali būti trumpesnis nei {{ limit }} simbolių",
     *     maxMessage = "Aprašymas negali būti ilgesnis kaip {{ limit }} simboliai"
     * )
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="Court")
     * @ORM\JoinColumn(name="court_id", referencedColumnName="id")
     *
     * @Assert\NotBlank(
     *     message = "Prašome pasirinkti aikštelę"
     * )
     * @Assert\Valid()
     */
    protected $court;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @Assert\DateTime(
     *     message = "Neteisingas mačo laikas"
     * )
     * @Assert\Range(
     *     min = "now",
     *     max = "+7 days",
     *     minMessage = "Blogai nurodytas mačo laikas",
     *     maxMessage = "Mačą galima kurti ne daugiau kaip 7 dienom į priekį"
     * )
     */
    protected $beginsAt;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $playersCount;

    /**
     * @ORM\ManyToOne(targetEntity="Type")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     *
     * @Assert\NotBlank(
     *     message = "Prašome pasirinkti mačo tipą"
     * )
     * @Assert\Valid()
     */
    protected $type;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @Assert\DateTime()
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default" : 0})
     */
    protected $active;

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
     * Set description
     *
     * @param string $description
     *
     * @return Match
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
     * Set beginsAt
     *
     * @param \DateTime $beginsAt
     *
     * @return Match
     */
    public function setBeginsAt($beginsAt)
    {
        $this->beginsAt = $beginsAt;

        return $this;
    }

    /**
     * Get beginsAt
     *
     * @return \DateTime
     */
    public function getBeginsAt()
    {
        return $this->beginsAt;
    }

    /**
     * Set type
     *
     * @param Type $type
     *
     * @return Match
     */
    public function setType(Type $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Match
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set court
     *
     * @param Court $court
     *
     * @return Match
     */
    public function setCourt(Court $court = null)
    {
        $this->court = $court;

        return $this;
    }

    /**
     * Get court
     *
     * @return Court
     */
    public function getCourt()
    {
        return $this->court;
    }

    /**
     * Set owner
     *
     * @param User $owner
     *
     * @return Match
     */
    public function setOwner(User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    public function increasePlayersCount()
    {
        $this->playersCount++;
    }

    public function decreasePlayersCount()
    {
        $this->playersCount--;
    }

    /**
     * Set players
     *
     * @param integer $playersCount
     *
     * @return Match
     */
    public function setPlayersCount($playersCount)
    {
        $this->playersCount = $playersCount;

        return $this;
    }

    /**
     * Get players
     *
     * @return integer
     */
    public function getPlayersCount()
    {
        return $this->playersCount;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Match
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Add player
     *
     * @param User $player
     *
     * @return Match
     */
    public function addPlayer(User $player)
    {
        $this->players[] = $player;

        return $this;
    }

    /**
     * Remove player
     *
     * @param User $player
     */
    public function removePlayer(User $player)
    {
        $this->players->removeElement($player);
    }

    /**
     * Get players
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * Add comment
     *
     * @param Comment $comment
     *
     * @return Match
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param Comment $comment
     */
    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }
}
