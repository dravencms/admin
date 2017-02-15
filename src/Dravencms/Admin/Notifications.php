<?php

namespace Dravencms\Admin;


/**
 * Class Notifications
 * @package Dravencms\Admin
 */
class Notifications extends \Nette\Object
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
