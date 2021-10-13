<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: sadam
 * Date: 15.2.17
 * Time: 13:20
 */

namespace Dravencms\Admin;


use Dravencms\Model\User\Entities\User;

interface INotificationArea
{
    const COUNT_TYPE_INFO = 'info';
    const COUNT_TYPE_DEFAULT = 'default';
    const COUNT_TYPE_SUCCESS = 'success';
    const COUNT_TYPE_WARNING = 'warning';
    const COUNT_TYPE_DANGER = 'danger';

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getIcon(): string;

    /**
     * @return string|null
     */
    public function getMoreUrl(): ?string;

    /**
     * @return array
     */
    public function getMoreUrlArguments(): array;

    /**
     * @return string
     */
    public function getMoreTitle(): string;

    /**
     * @param integer $count
     * @return string
     */
    public function getCountType(int $count): string;

    /**
     * @return string
     */
    public function getNotificationTemplate(): string;

    /**
     * @param User $user
     * @return INotification[]
     */
    public function getNotifications(User $user): array;

    /**
     * @return string|null
     */
    public function getAclResource(): ?string;

    /**
     * @return string|null
     */
    public function getAclOperation(): ?string;
}