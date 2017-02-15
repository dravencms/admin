<?php
/**
 * Created by PhpStorm.
 * User: sadam
 * Date: 15.2.17
 * Time: 14:05
 */

namespace Dravencms\Admin;


class Notification implements INotification
{
    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var string */
    private $type;

    /** @var string */
    private $icon;

    /** @var null|string */
    private $url;

    /** @var array */
    private $urlArguments;

    /** @var \DateTimeInterface|null */
    private $createdAt;
    /**
     * Notification constructor.
     * @param $name
     * @param $description
     * @param $icon
     * @param string $type
     * @param null $url
     * @param array $urlArguments
     * @param \DateTimeInterface|null $createdAt
     */
    public function __construct($name, $description, $icon, $type = INotification::TYPE_DEFAULT, $url = null, $urlArguments = [], \DateTimeInterface $createdAt = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->type = $type;
        $this->icon = $icon;
        $this->url = $url;
        $this->urlArguments = $urlArguments;
        $this->createdAt = $createdAt;
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
     * @return null|string
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
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}