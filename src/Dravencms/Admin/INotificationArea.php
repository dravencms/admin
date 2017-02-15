<?php
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
    public function getName();

    /**
     * @return string
     */
    public function getIcon();

    /**
     * @return string|null
     */
    public function getMoreUrl();

    /**
     * @return array
     */
    public function getMoreUrlArguments();

    /**
     * @return string
     */
    public function getMoreTitle();

    /**
     * @param integer $count
     * @return string
     */
    public function getCountType($count);

    /**
     * @return string
     */
    public function getNotificationTemplate();

    /**
     * @param User $user
     * @return INotification
     */
    public function getNotifications(User $user);

    /**
     * @return string|null
     */
    public function getAclResource();

    /**
     * @return string|null
     */
    public function getAclOperation();
}