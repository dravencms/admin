<?php declare(strict_types = 1);
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Admin\Entities;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Dravencms\Database\Attributes\Identifier;
use Dravencms\Model\User\Entities\User;
use Nette\SmartObject;


/**
 * Class NotificationViewed
 * @ORM\Entity
 * @ORM\Table(name="adminNotificationViewed")
 */
class NotificationViewed
{
    use SmartObject;
    use Identifier;
    use TimestampableEntity;

    /**
     * @var Notification
     * @ORM\ManyToOne(targetEntity="Notification", inversedBy="notificationsViewed")
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
    public function setNotification(Notification $notification): void
    {
        $this->notification = $notification;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Notification
     */
    public function getNotification(): Notification
    {
        return $this->notification;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
