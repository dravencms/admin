<?php declare(strict_types = 1);

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
    public function addNotificationAreaProvider(INotificationArea $notificationArea): void
    {
        $this->notificationAreaProviders[] = $notificationArea;
    }

    /**
     * @return INotificationArea[]
     */
    public function getNotificationAreaProviders(): array
    {
        return $this->notificationAreaProviders;
    }
}
