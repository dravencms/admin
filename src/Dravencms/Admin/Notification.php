<?php

namespace Dravencms\Admin;


/**
 * Class Notification
 * @package Dravencms\Admin
 */
class Notification extends \Nette\Object
{
    /**
     * @var array
     */
    private $notificationAreaProviders = [];

    /**
     * @param INotificationArea $notificationArea
     */
    public function addNotificationAreaProvider(INotificationArea $notificationArea)
    {
        $this->notificationAreaProviders[] = $notificationArea;
    }

    /**
     * @return INotificationArea[]
     */
    public function getNotificationAreaProviders()
    {
        return $this->notificationAreaProviders;
    }
}
