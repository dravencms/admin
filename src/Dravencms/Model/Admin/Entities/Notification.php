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
 * Class Notification
 * @ORM\Entity
 * @ORM\Table(name="adminNotification")
 */
class Notification extends Nette\Object implements INotification
{
    public static $typeList = [
        INotification::TYPE_INFO,
        INotification::TYPE_DEFAULT,
        INotification::TYPE_SUCCESS,
        INotification::TYPE_WARNING,
        INotification::TYPE_DANGER
    ];

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
     * Notification constructor.
     * @param string $name
     * @param string $description
     * @param string $type
     * @param string $icon
     * @param string $url
     * @param array $urlArguments
     * @param User $user
     * @param AclOperation $aclOperation
     */
    public function __construct($name, $description, $icon, $type = INotification::TYPE_DEFAULT, $url = null, array $urlArguments = [], User $user = null, AclOperation $aclOperation = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->type = $type;
        $this->icon = $icon;
        $this->url = $url;
        $this->urlArguments = $urlArguments;
        $this->user = $user;
        $this->aclOperation = $aclOperation;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param $type
     * @throws \Exception
     */
    public function setType($type)
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
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @param array $urlArguments
     */
    public function setUrlArguments(array $urlArguments)
    {
        $this->urlArguments = $urlArguments;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param AclOperation $aclOperation
     */
    public function setAclResource(AclOperation $aclOperation)
    {
        $this->aclOperation = $aclOperation;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return array
     */
    public function getUrlArguments()
    {
        return $this->urlArguments;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return AclOperation
     */
    public function getAclOperation()
    {
        return $this->aclOperation;
    }
}