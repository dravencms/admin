<?php declare(strict_types = 1);
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Admin\Entities;

use Doctrine\Common\Collections\Collection;
use Dravencms\Model\User\Entities\AclOperation;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Dravencms\Database\Attributes\Identifier;
use Nette;

/**
 * Class Menu
 * @package Dravencms\Model\Structure\Entities
 * @Gedmo\Tree(type="nested")
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 * @ORM\Table(name="adminMenu")
 */
class Menu
{
    use Nette\SmartObject;
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
     * @var Menu[]
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
        string $name,
        string $presenter = null,
        string $icon = null,
        AclOperation $aclOperation = null,
        string $action = null,
        bool $isActive = true,
        bool $isHomePage = false
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
    public function setParent(Menu $parent = null): Menu
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @param boolean $isHomePage
     */
    public function setIsHomePage(bool $isHomePage): void
    {
        $this->isHomePage = $isHomePage;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param boolean $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @param string $presenter
     */
    public function setPresenter(string $presenter): void
    {
        $this->presenter = $presenter;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * @return boolean
     */
    public function isHomePage(): bool
    {
        return $this->isHomePage;
    }


    /**
     * @return Menu|null
     */
    public function getParent(): ?Menu
    {
        return $this->parent;
    }

    /**
     * @return Menu[]|Collection
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @return int
     */
    public function getLvl(): int
    {
        return $this->lvl;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return boolean
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return boolean
     */
    public function isIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return boolean
     */
    public function isIsHomePage(): bool
    {
        return $this->isHomePage;
    }

    /**
     * @return string
     */
    public function getPresenter(): ?string
    {
        return $this->presenter;
    }

    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return $this->action;
    }
}
