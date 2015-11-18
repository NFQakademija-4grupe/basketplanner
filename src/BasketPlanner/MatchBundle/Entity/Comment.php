<?php

namespace BasketPlanner\MatchBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use BasketPlanner\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table(name="comments")
 * @ORM\Entity
 */
class Comment
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
     * @ORM\ManyToOne(targetEntity="\BasketPlanner\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Match
     *
     * @ORM\ManyToOne(targetEntity="Match", inversedBy="comments")
     */
    private $match;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255)
     *
     * @Assert\NotBlank(
     *     message = "Žinutė negali būti tuščia"
     * )
     * @Assert\Length(
     *     max = 255,
     *     maxMessage = "Žinutė negali būti ilgesnis kaip {{ limit }} simboliai"
     * )
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;


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
     * Set message
     *
     * @param string $message
     *
     * @return Comment
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Comment
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
     * Set user
     *
     * @param User $user
     *
     * @return Comment
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set match
     *
     * @param Match $match
     *
     * @return Comment
     */
    public function setMatch(Match $match = null)
    {
        $this->match = $match;

        return $this;
    }

    /**
     * Get match
     *
     * @return Match
     */
    public function getMatch()
    {
        return $this->match;
    }
}
