<?php

namespace BasketPlanner\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="notifications")
 */
class Notification
{
    private $id;
    private $title;
    private $text;
    private $link;
    private $date;

}