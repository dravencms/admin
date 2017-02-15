<?php
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
    public function getName();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getIcon();

    /**
     * @return string|null
     */
    public function getUrl();

    /**
     * @return array
     */
    public function getUrlArguments();

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt();
}