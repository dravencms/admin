<?php

namespace Dravencms\Admin;

use Nette;

/**
 * Class Notifications
 * @package Dravencms\Admin
 */
class Notifications
{
    use Nette\SmartObject;
    
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
