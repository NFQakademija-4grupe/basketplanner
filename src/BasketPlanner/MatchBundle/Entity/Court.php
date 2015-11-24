<?php

namespace BasketPlanner\MatchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Court
 *
 * @ORM\Table(name="courts")
 * @ORM\Entity()
 */
class Court
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
     * @ORM\Column(name="address", type="string", length=50)
     *
     * @Assert\NotBlank(
     *     message = "Adresas negali būti tusčias"
     * )
     * @Assert\Length(
     *     min = 5,
     *     max = 50,
     *     minMessage = "Neteisingas adresas",
     *     maxMessage  = "Neteisingas adresas"
     * )
     */
    private $address;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="decimal", precision=18, scale=18, nullable=true)
     *
     * @Assert\NotBlank(
     *     message = "Blogai nurodytos koordinates"
     * )
     * @Assert\Type(
     *     type  = "float",
     *     message = "Neteisingas koordinates tipas"
     * )
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="decimal", precision=18, scale=18, nullable=true)
     *
     * @Assert\NotBlank(
     *     message = "Blogai nurodytos koordinates"
     * )
     * @Assert\Type(
     *     type  = "float",
     *     message = "Neteisingas koordinates tipas"
     * )
     */
    private $longitude;

    /**
     * @ORM\Column(name="approved", type="boolean")
     */
    private $approved;

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
     * Set id
     *
     * @param integer $id
     *
     * @return Court
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Court
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     *
     * @return Court
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     *
     * @return Court
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set approved
     *
     * @param boolean $approved
     *
     * @return Court
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;

        return $this;
    }

    /**
     * Get approved
     *
     * @return boolean
     */
    public function getApproved()
    {
        return $this->approved;
    }
}
