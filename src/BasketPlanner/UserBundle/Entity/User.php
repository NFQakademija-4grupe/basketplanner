<?php

namespace BasketPlanner\UserBundle\Entity;

use BasketPlanner\MatchBundle\Entity\Match;
use BasketPlanner\TeamBundle\Entity\TeamUser;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Entity\User as BaseUser;
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
     * @var int
     *
     * @ORM\Column(name="age", type="integer", length=2, nullable=true)
     */
    protected $age;

    /**
     * @var string
     *
     * @ORM\Column(name="picture_url", type="string", length=500, nullable=true)
     */
    protected $profilePicture;

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
    protected $facebookAccessToken;

    /**
     * @var string
     *
     * @ORM\Column(name="google_id", type="string", nullable=true)
     */
    private $googleId;

    /**
     * @ORM\OneToMany(targetEntity="\BasketPlanner\MatchBundle\Entity\Match", mappedBy="owner")
     **/
    protected $createdMatches;

    /**
     * @ORM\ManyToMany(targetEntity="\BasketPlanner\MatchBundle\Entity\Match", mappedBy="players")
     */
    protected $joinedMatches;

    /**
     * @var string
     *
     * @ORM\Column(name="google_access_token", type="string", length=255, nullable=true)
     */
    protected $googleAccessToken;

    /**
     * @ORM\OneToMany(targetEntity="BasketPlanner\UserBundle\Entity\NotificationUser", mappedBy="user")
     */
    protected $notificationUser;

    /**
     * @ORM\OneToMany(targetEntity="BasketPlanner\TeamBundle\Entity\TeamUser", mappedBy="user")
     */
    protected $teamUser;

    public function __construct()
    {
        parent::__construct();
        $this->notificationUser = new ArrayCollection();
        $this->teamUser = new ArrayCollection();
        $this->createdMatches = new ArrayCollection();
        $this->joinedMatches = new ArrayCollection();
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
     * Get age
     *
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set age
     *
     * @param int $age
     *
     * @return User
     */
    public function setAge($age)
    {
        $this->age = $age;
        return $this;
    }

    /**
     * Get profile picture url
     *
     * @return string
     */
    public function getProfilePicture()
    {
        return $this->profilePicture;
    }

    /**
     * Set profile picture url
     *
     * @param string $profilePicture
     *
     * @return User
     */
    public function setProfilePicture($profilePicture)
    {
        $this->profilePicture = $profilePicture;
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
        return $this->facebookAccessToken;
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
        $this->facebookAccessToken = $facebookAccessToken;
        return $this;
    }

    /**
     * Get google Access Token
     *
     * @return string
     */
    public function getGoogleAccessToken()
    {
        return $this->googleAccessToken;
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
        $this->googleAccessToken = $googleAccessToken;
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
     * Add createdMatch
     *
     * @param Match $createdMatch
     *
     * @return User
     */
    public function addCreatedMatch(Match $createdMatch)
    {
        $this->createdMatches[] = $createdMatch;

        return $this;
    }

    /**
     * Remove createdMatch
     *
     * @param Match $createdMatch
     */
    public function removeCreatedMatch(Match $createdMatch)
    {
        $this->createdMatches->removeElement($createdMatch);
    }

    /**
     * Get createdMatches
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCreatedMatches()
    {
        return $this->createdMatches;
    }

    /**
     * Add joinedMatch
     *
     * @param Match $joinedMatch
     *
     * @return User
     */
    public function addJoinedMatch(Match $joinedMatch)
    {
        $this->joinedMatches[] = $joinedMatch;

        return $this;
    }

    /**
     * Remove joinedMatch
     *
     * @param Match $joinedMatch
     */
    public function removeJoinedMatch(Match $joinedMatch)
    {
        $this->joinedMatches->removeElement($joinedMatch);
    }

    /**
     * Get joinedMatches
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getJoinedMatches()
    {
        return $this->joinedMatches;
    }

    /**
     * Add notification to user
     *
     * @param \BasketPlanner\UserBundle\Entity\NotificationUser $notification
     * @return User
     */
    public function addNotificationUser(NotificationUser $notification)
    {
        $this->notificationUser[] = $notification;

        return $this;
    }

    /**
     * Remove notification from user
     *
     * @param \BasketPlanner\UserBundle\Entity\NotificationUser $notification
     */
    public function removeNotificationUser(NotificationUser $notification)
    {
        $this->notificationUser->removeElement($notification);
    }

    /**
     * Get notifications to user
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotificationUser()
    {
        return $this->notificationUser;
    }

    //

    /**
     * Add team
     *
     * @param \BasketPlanner\TeamBundle\Entity\TeamUser $team
     * @return User
     */
    public function addTeamUser(TeamUser $team)
    {
        $this->teamUser[] = $team;

        return $this;
    }

    /**
     * Remove team
     *
     * @param \BasketPlanner\TeamBundle\Entity\TeamUser $team
     */
    public function removeTeamUser(TeamUser $team)
    {
        $this->teamUser->removeElement($team);
    }

    /**
     * Get team where
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTeamUser()
    {
        return $this->teamUser;
    }

}
