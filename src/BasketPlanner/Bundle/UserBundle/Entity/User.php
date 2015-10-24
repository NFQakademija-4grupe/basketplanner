<?php

namespace BasketPlanner\Bundle\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

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
    public function setFacebookID($facebookId)
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

}
