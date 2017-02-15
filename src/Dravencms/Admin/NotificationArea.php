<?php
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
    public function getName()
    {
        return 'Notifications';
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return 'bullhorn';
    }

    /**
     * @return null
     */
    public function getMoreUrl()
    {
        return null;
    }

    /**
     * @return array
     */
    public function getMoreUrlArguments()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getMoreTitle()
    {
        return 'More notifications';
    }

    /**
     * @param $count
     * @return string
     */
    public function getCountType($count)
    {
        return ($count > 0 ? INotificationArea::COUNT_TYPE_WARNING : INotificationArea::COUNT_TYPE_SUCCESS);
    }

    /**
     * @return string
     */
    public function getNotificationTemplate()
    {
        return __DIR__.'/NotificationArea.latte';
    }

    /**
     * @param User $user
     * @return \Dravencms\Model\Admin\Entities\Notification[]
     */
    public function getNotifications(User $user)
    {
        return $this->notificationRepository->getForUser($user);
    }

    /**
     * @return null
     */
    public function getAclResource()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getAclOperation()
    {
        return null;
    }
}