<?php

namespace BasketPlanner\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="notifications")
 */
class Notification
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
     * @ORM\Column(name="title", type="string", length=80)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=255)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255)
     */
    private $link;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Assert\DateTime()
     */
    private $date;

    /**
     *  notification - user association
     *
     * @ORM\OneToMany(targetEntity="BasketPlanner\UserBundle\Entity\NotificationUser", mappedBy="notification")
     */
    protected $notificationUser;

    public function __construct()
    {
        $this->notificationUser = new ArrayCollection();
    }
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
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Add notificationUser
     *
     * @param \BasketPlanner\UserBundle\Entity\NotificationUser $notifiedUser
     * @return Notification
     */
    public function addNotificationUser($notifiedUser)
    {
        $this->notificationUser[] = $notifiedUser;

        return $this;
    }

    /**
     * Remove notificationUser
     *
     * @param \BasketPlanner\UserBundle\Entity\NotificationUser $notifiedUser
     */
    public function removeNotificationUser($notifiedUser)
    {
        $this->notificationUser->removeElement($notifiedUser);
    }

    /**
     * Get notificationUser
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotificationUser()
    {
        return $this->notificationUser;
    }

}