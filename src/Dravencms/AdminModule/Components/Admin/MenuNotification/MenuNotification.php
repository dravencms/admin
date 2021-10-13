<?php declare(strict_types = 1);

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */


namespace Dravencms\AdminModule\Components\Admin\MenuNotification;

use Dravencms\Admin\Notifications;
use Dravencms\Components\BaseControl\BaseControl;

use Dravencms\Model\User\Entities\User;

class MenuNotification extends BaseControl
{
    /** @var User */
    private $user;

    /** @var Notifications */
    private $notifications;

    /**
     * MenuNotification constructor.
     * @param User $user
     * @param Notifications $notifications
     */
    public function __construct(User $user, Notifications $notifications)
    {
        $this->user = $user;
        $this->notifications = $notifications;
    }


    public function render(): void
    {
        $template = $this->template;

        $template->notificationAreas = $this->notifications->getNotificationAreaProviders();
        $template->user = $this->user;
        $template->setFile(__DIR__ . '/MenuNotification.latte');
        $template->render();
    }
}