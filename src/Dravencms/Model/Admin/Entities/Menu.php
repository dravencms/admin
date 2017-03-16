<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Admin\Entities;

use Dravencms\Model\User\Entities\AclOperation;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;

/**
 * Class Menu
 * @package Dravencms\Model\Structure\Entities
 * @Gedmo\Tree(type="nested")
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 * @ORM\Table(name="adminMenu")
 */
class Menu extends Nette\Object
{
    use Identifier;
    use TimestampableEntity;

    /**
     * @var string
     * @Gedmo\Translatable
     * @ORM\Column(type="string",length=255,nullable=false)
     */
    private $name;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isActive;

    /**
     * @var string
     * @ORM\Column(type="string",length=255, nullable=true)
     */
    private $presenter;

    /**
     * @var string
     * @ORM\Column(type="string",length=255, nullable=true)
     */
    private $action;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isHomePage;

    /**
     * @var string
     * @ORM\Column(type="string",length=255, nullable=true)
     */
    private $icon;

    /**
     * @var AclOperation
     * @ORM\ManyToOne(targetEntity="Dravencms\Model\User\Entities\AclOperation", inversedBy="adminMenus")
     * @ORM\JoinColumn(name="acloperation_id", referencedColumnName="id")
     */
    private $aclOperation;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     * and it is not necessary because globally locale can be set in listener
     */
    private $locale;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Menu", mappedBy="parent")
     */
    private $children;

    /**
     * Menu constructor.
     * @param $name
     * @param $presenter
     * @param $icon
     * @param AclOperation $aclOperation
     * @param string $action
     * @param bool $isActive
     * @param bool $isHomePage
     */
    public function __construct(
        $name,
        $presenter,
        $icon,
        AclOperation $aclOperation = null,
        $action = 'default',
        $isActive = true,
        $isHomePage = false
    ) {
        $this->name = $name;
        $this->icon = $icon;
        $this->aclOperation = $aclOperation;
        $this->isActive = $isActive;
        $this->isHomePage = $isHomePage;
        $this->presenter = $presenter;
        $this->action = $action;
    }

    /**
     * @param Menu|null $parent
     * @return $this
     */
    public function setParent(Menu $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @param boolean $isHomePage
     */
    public function setIsHomePage($isHomePage)
    {
        $this->isHomePage = $isHomePage;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @param string $presenter
     */
    public function setPresenter($presenter)
    {
        $this->presenter = $presenter;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return boolean
     */
    public function isHomePage()
    {
        return $this->isHomePage;
    }
    
    /**
     * @param $locale
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return mixed
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @return boolean
     */
    public function isIsActive()
    {
        return $this->isActive;
    }

    /**
     * @return boolean
     */
    public function isIsHomePage()
    {
        return $this->isHomePage;
    }

    /**
     * @return string
     */
    public function getPresenter()
    {
        return $this->presenter;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
}