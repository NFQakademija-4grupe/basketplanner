<?php

namespace BasketPlanner\MatchBundle\Entity;

use BasketPlanner\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
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
     * @ORM\OneToOne(targetEntity="\BasketPlanner\UserBundle\Entity\User", inversedBy="match")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     * @Assert\Type(type="\BasketPlanner\UserBundle\Entity\User")
     * @Assert\Valid()
     **/
    protected $user;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(
     *     message = "Aprašymas negali būti tuščias"
     * )
     * @Assert\Length(
     *     min = 25,
     *     max = 255,
     *     minMessage = "Aprašymas negali būti trumpesnis nei {{ limit }} simboliai",
     *     maxMessage = "Aprašymas negali būti ilgesnis kaip {{ limit }} simboliai"
     * )
     */
    protected $description;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Assert\DateTime(
     *     message = "Blogai nurodytas mačo laikas"
     * )
     * @Assert\Range(
     *     min = "now",
     *     minMessage = "Blogai nurodytas mačo laikas"
     * )
     */
    protected $beginsAt;

    /**
     * @ORM\Column(type="string", length=25)
     *
     * @Assert\NotBlank(
     *     message = "Rajonas negali būti tuščias"
     * )
     * @Assert\Length(
     *     min = 5,
     *     max = 25,
     *     minMessage = "Rajono pavadinimas negali būti trumpesnis nei {{ limit }} simboliai",
     *     maxMessage = "Rajono pavadinimas negali būti ilgesnis kaip {{ limit }} simboliai"
     * )
     */
    protected $district;

    /**
     * @ORM\Column(type="float")
     *
     * @Assert\NotBlank(
     *     message = "Nenurodyta platuma"
     * )
     * @Assert\Type(
     *     type="double",
     *     message = "Blogai nurodyta koordinate(platuma)"
     * )
     */
    protected $latitude;

    /**
     * @ORM\Column(type="float")
     *
     * @Assert\NotBlank(
     *     message = "Nenurodyta ilguma"
     * )
     * @Assert\Type(
     *     type="double",
     *     message = "Blogai nurodyta koordinate(ilguma)"
     * )
     */
    protected $longitude;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('1x1', '2x2', '3x3', '4x4', '5x5', 'Nesvarbu')")
     *
     * @Assert\Choice(
     *     choices = {"1x1", "2x2", "3x3", "4x4", "5x5", "Nesvarbu"},
     *     message = "Neteisingas mačo tipas"
     * )
     */
    protected $type;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Assert\DateTime()
     */
    protected $createdAt;


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
     * Set district
     *
     * @param string $district
     *
     * @return Match
     */
    public function setDistrict($district)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * Get district
     *
     * @return string
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * Set latitude
     *
     * @param \double $latitude
     *
     * @return Match
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return \double
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param \double $longitude
     *
     * @return Match
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return \double
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Match
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
     * Set user
     *
     * @param User $user
     *
     * @return Match
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
}
