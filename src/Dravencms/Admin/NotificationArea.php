<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: sadam
 * Date: 15.2.17
 * Time: 13:20
 */

namespace Dravencms\Admin;


use Dravencms\Model\Admin\Repository\NotificationRepository;
use Dravencms\Model\User\Entities\User;

class NotificationArea implements INotificationArea
{
    /** @var NotificationRepository */
    private $notificationRepository;

    /**
     * NotificationArea constructor.
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'Notifications';
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return 'bullhorn';
    }

    /**
     * @return null|string
     */
    public function getMoreUrl(): ?string
    {
        return null;
    }

    /**
     * @return array
     */
    public function getMoreUrlArguments(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function getMoreTitle(): string
    {
        return 'More notifications';
    }

    /**
     * @param $count
     * @return string
     */
    public function getCountType(int $count): string
    {
        return ($count > 0 ? INotificationArea::COUNT_TYPE_WARNING : INotificationArea::COUNT_TYPE_SUCCESS);
    }

    /**
     * @return string
     */
    public function getNotificationTemplate(): string
    {
        return __DIR__.'/NotificationArea.latte';
    }

    /**
     * @param User $user
     * @return \Dravencms\Model\Admin\Entities\Notification[]
     */
    public function getNotifications(User $user): array
    {
        return $this->notificationRepository->getForUser($user);
    }

    /**
     * @return string|null
     */
    public function getAclResource(): ?string
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getAclOperation(): ?string
    {
        return null;
    }
}