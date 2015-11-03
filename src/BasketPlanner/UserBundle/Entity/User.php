<?php

namespace BasketPlanner\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=50, nullable=true)
     *
     * @Assert\NotBlank(message="Įveskite savo vardą.", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=3,
     *     max=50,
     *     minMessage="Vardas per trumpas.",
     *     maxMessage="Vardas per ilgas.",
     *     groups={"Registration", "Profile"}
     * )
     * @Assert\Regex(
     *        pattern="/[a-zA-Z]/",
     *        message="Vardas gali būti sudarytas tik iš raidžių symbolių.",
     *        groups={"Registration", "Profile"}
     * )
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=50, nullable=true)
     *
     * @Assert\NotBlank(message="Įveskite savo pavardę.", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=3,
     *     max=50,
     *     minMessage="Pavarde per trumpa.",
     *     maxMessage="Pavarde per ilga.",
     *     groups={"Registration", "Profile"}
     * )
     * @Assert\Regex(
     *        pattern="/[a-zA-Z]/",
     *        message="Pavardė gali būti sudaryta tik iš raidžių symbolių.",
     *        groups={"Registration", "Profile"}
     * )
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=10, nullable=true)
     */
    protected $gender;

    /**
     * @ORM\Column(name="profile_updated", type="boolean", nullable=true)
     */
    protected $profileUpdated = false;


    /**
     * @var string
     *
     * @ORM\Column(name="facebook_id", type="string", nullable=true)
     */
    private $facebookId;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true)
     */
    protected $facebook_access_token;

    /**
     * @var string
     *
     * @ORM\Column(name="google_id", type="string", nullable=true)
     */
    private $googleId;

    /**
     * @ORM\OneToOne(targetEntity="\BasketPlanner\MatchBundle\Entity\Match", mappedBy="user")
     **/
    protected $match;

    /**
     * @var string
     *
     * @ORM\Column(name="google_access_token", type="string", length=255, nullable=true)
     */
    protected $google_access_token;

    public function __construct()
    {
        parent::__construct();
        // your own logic
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
     * Get first name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set first name
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * Get last name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Get full name
     *
     * @return string
     */
    public function getFullName()
    {
        $fullName = $this->firstName." ".$this->lastName;
        return $fullName;
    }

    /**
     * Set last name
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * Get facebook Id
     *
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * Set facebook Id
     *
     * @param string $facebookId
     *
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
        return $this;
    }

    /**
     * Get google Id
     *
     * @return string
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }

    /**
     * Set google Id
     *
     * @param string $googleId
     *
     * @return User
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;
        return $this;
    }

    /**
     * Get facebook Access Token
     *
     * @return string
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }

    /**
     * Set facebook Access Token
     *
     * @param string $facebookAccessToken
     *
     * @return User
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebook_access_token = $facebookAccessToken;
        return $this;
    }

    /**
     * Get google Access Token
     *
     * @return string
     */
    public function getGoogleAccessToken()
    {
        return $this->google_access_token;
    }

    /**
     * Set google Access Token
     *
     * @param string $googleAccessToken
     *
     * @return User
     */
    public function setGoogleAccessToken($googleAccessToken)
    {
        $this->google_access_token = $googleAccessToken;
        return $this;
    }


    /**
     * Set profileUpdated
     *
     * @param boolean $profileUpdated
     *
     * @return User
     */
    public function setProfileUpdated($profileUpdated)
    {
        $this->profileUpdated = $profileUpdated;

        return $this;
    }

    /**
     * Get profileUpdated
     *
     * @return boolean
     */
    public function getProfileUpdated()
    {
        return $this->profileUpdated;
    }

    /**
     * Set match
     *
     * @param \BasketPlanner\MatchBundle\Entity\Match $match
     *
     * @return User
     */
    public function setMatch(\BasketPlanner\MatchBundle\Entity\Match $match = null)
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
     * Add comment
     *
     * @param \BasketPlanner\MatchBundle\Entity\Comment $comment
     *
     * @return User
     */
    public function addComment(\BasketPlanner\MatchBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \BasketPlanner\MatchBundle\Entity\Comment $comment
     */
    public function removeComment(\BasketPlanner\MatchBundle\Entity\Comment $comment)
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
