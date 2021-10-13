<?php declare(strict_types = 1);
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Admin\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Dravencms\Admin\INotification;
use Dravencms\Model\User\Entities\AclOperation;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Dravencms\Database\Attributes\Identifier;
use Nette;
use Dravencms\Model\User\Entities\User;


/**
 * Class Notification
 * @ORM\Entity
 * @ORM\Table(name="adminNotification")
 */
class Notification implements INotification
{
    public static $typeList = [
        INotification::TYPE_INFO,
        INotification::TYPE_DEFAULT,
        INotification::TYPE_SUCCESS,
        INotification::TYPE_WARNING,
        INotification::TYPE_DANGER
    ];

    use Nette\SmartObject;
    use Identifier;
    use TimestampableEntity;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="string",length=255, nullable=false)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string",length=255, nullable=false)
     */
    private $icon;

    /**
     * @var string
     * @ORM\Column(type="string",length=255, nullable=true)
     */
    private $url;

    /**
     * @var array
     * @ORM\Column(type="json_array",nullable=true)
     */
    private $urlArguments;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Dravencms\Model\User\Entities\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id",nullable=true)
     */
    private $user;

    /**
     * @var AclOperation
     * @ORM\ManyToOne(targetEntity="Dravencms\Model\User\Entities\AclOperation")
     * @ORM\JoinColumn(name="acl_operation_id", referencedColumnName="id",nullable=true)
     */
    private $aclOperation;

    /**
     * @var NotificationViewed[]
     * @ORM\OneToMany(targetEntity="NotificationViewed", mappedBy="notification")
     */
    private $notificationsViewed;

    /**
     * Notification constructor.
     * @param string $name
     * @param string $description
     * @param string $icon
     * @param string $type
     * @param string|null $url
     * @param array $urlArguments
     * @param User|null $user
     * @param AclOperation|null $aclOperation
     */
    public function __construct(string $name, string $description, string $icon, $type = INotification::TYPE_DEFAULT, string $url = null, array $urlArguments = [], User $user = null, AclOperation $aclOperation = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->type = $type;
        $this->icon = $icon;
        $this->url = $url;
        $this->urlArguments = $urlArguments;
        $this->user = $user;
        $this->aclOperation = $aclOperation;
        $this->notificationsViewed = new ArrayCollection();
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @param $type
     * @throws \Exception
     */
    public function setType(string $type): void
    {
        if (!in_array($type, self::$typeList))
        {
            throw new \Exception('Wrong $type value');
        }
        $this->type = $type;
    }

    /**
     * @param string $icon
     */
    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @param array $urlArguments
     */
    public function setUrlArguments(array $urlArguments): void
    {
        $this->urlArguments = $urlArguments;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @param AclOperation $aclOperation
     */
    public function setAclResource(AclOperation $aclOperation): void
    {
        $this->aclOperation = $aclOperation;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return array
     */
    public function getUrlArguments(): array
    {
        return $this->urlArguments;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return AclOperation
     */
    public function getAclOperation(): AclOperation
    {
        return $this->aclOperation;
    }

    /**
     * @return array
     */
    public function getNotificationsViewed(): array
    {
        return $this->notificationsViewed;
    }
}
