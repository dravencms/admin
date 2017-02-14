<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Admin\Entities;

use Doctrine\ORM\Mapping as ORM;
use Dravencms\Model\User\Entities\AclResource;
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
class Notification extends Nette\Object
{
    const TYPE_INFO = 'info';
    const TYPE_DEFAULT = 'default';
    const TYPE_SUCCESS = 'success';
    const TYPE_WARNING = 'warning';
    const TYPE_DANGER = 'danger';

    public static $typeList = [
        self::TYPE_INFO,
        self::TYPE_DEFAULT,
        self::TYPE_SUCCESS,
        self::TYPE_WARNING,
        self::TYPE_DANGER
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
     * @var AclResource
     * @ORM\ManyToOne(targetEntity="Dravencms\Model\User\Entities\AclResource")
     * @ORM\JoinColumn(name="acl_resource_id", referencedColumnName="id",nullable=true)
     */
    private $aclResource;

    /**
     * Notification constructor.
     * @param string $name
     * @param string $description
     * @param string $type
     * @param string $icon
     * @param string $url
     * @param array $urlArguments
     * @param User $user
     * @param AclResource $aclResource
     */
    public function __construct($name, $description, $icon, $type = self::TYPE_DEFAULT, $url = null, array $urlArguments = [], User $user = null, AclResource $aclResource = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->type = $type;
        $this->icon = $icon;
        $this->url = $url;
        $this->urlArguments = $urlArguments;
        $this->user = $user;
        $this->aclResource = $aclResource;
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
     * @param AclResource $aclResource
     */
    public function setAclResource(AclResource $aclResource)
    {
        $this->aclResource = $aclResource;
    }


}