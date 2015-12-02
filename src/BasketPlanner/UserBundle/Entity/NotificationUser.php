<?php

namespace BasketPlanner\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="notifications_users")
 */
class NotificationUser
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="notificationUser")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     */
    private $user;

    /**
     *
     * @ORM\ManyToOne(targetEntity="BasketPlanner\UserBundle\Entity\Notification", inversedBy="notificationUser")
     * @ORM\JoinColumn(name="notification_id", referencedColumnName="id")
     *
     */
    private $notification;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default" : 0})
     */
    private $seen;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set user
     *
     * @param \BasketPlanner\UserBundle\Entity\User $user
     * @return NotificationUser
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
     * Set notification
     *
     * @param \BasketPlanner\UserBundle\Entity\Notification $notification
     * @return NotificationUser
     */
    public function setNotification($notification = null)
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * Get notification
     *
     * @return \BasketPlanner\UserBundle\Entity\Notification
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @return mixed
     */
    public function getSeen()
    {
        return $this->seen;
    }

    /**
     * @param mixed $seen
     */
    public function setSeen($seen)
    {
        $this->seen = $seen;
    }

}