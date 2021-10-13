<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: sadam
 * Date: 15.2.17
 * Time: 13:19
 */

namespace Dravencms\Admin;

/**
 * Interface INotification
 * @package Dravencms\Admin
 */
interface INotification
{
    const TYPE_INFO = 'info';
    const TYPE_DEFAULT = 'default';
    const TYPE_SUCCESS = 'success';
    const TYPE_WARNING = 'warning';
    const TYPE_DANGER = 'danger';

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return string
     */
    public function getIcon(): string;

    /**
     * @return string|null
     */
    public function getUrl(): ?string;

    /**
     * @return array
     */
    public function getUrlArguments(): array;

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt();
}