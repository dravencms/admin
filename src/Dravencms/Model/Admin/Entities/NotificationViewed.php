<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Admin\Entities;

use Doctrine\ORM\Mapping as ORM;
use Dravencms\Admin\INotification;
use Dravencms\Model\User\Entities\AclOperation;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Dravencms\Model\User\Entities\User;


/**
 * Class NotificationViewed
 * @ORM\Entity
 * @ORM\Table(name="adminNotificationViewed")
 */
class NotificationViewed extends Nette\Object
{
    use Identifier;
    use TimestampableEntity;

    /**
     * @var Notification
     * @ORM\ManyToOne(targetEntity="Notification")
     * @ORM\JoinColumn(name="notification_id", referencedColumnName="id",nullable=true)
     */
    private $notification;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Dravencms\Model\User\Entities\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id",nullable=true)
     */
    private $user;

    /**
     * NotificationViewed constructor.
     * @param Notification $notification
     * @param User $user
     */
    public function __construct(Notification $notification, User $user)
    {
        $this->notification = $notification;
        $this->user = $user;
    }

    /**
     * @param Notification $notification
     */
    public function setNotification(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return Notification
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}