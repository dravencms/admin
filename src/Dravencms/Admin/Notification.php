<?php declare(strict_types = 1);
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
     * @param string $name
     * @param string $description
     * @param string $icon
     * @param string $type
     * @param string|null $url
     * @param array $urlArguments
     * @param \DateTimeInterface|null $createdAt
     */
    public function __construct(string $name, string $description, string $icon, $type = INotification::TYPE_DEFAULT, string $url = null, array $urlArguments = [], \DateTimeInterface $createdAt = null)
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
     * @return null|string
     */
    public function getUrl(): ?string
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
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }
}