<?php

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */


namespace Dravencms\AdminModule\Components\Admin\MenuNotification;

use Dravencms\Admin\Notification;
use Dravencms\Components\BaseControl\BaseControl;

use Dravencms\Model\User\Entities\User;

class MenuNotification extends BaseControl
{
    /** @var User */
    private $user;

    /** @var Notification */
    private $notification;

    /**
     * MenuNotification constructor.
     * @param User $user
     * @param Notification $notification
     */
    public function __construct(User $user, Notification $notification)
    {
        parent::__construct();

        $this->user = $user;
        $this->notification = $notification;
    }


    public function render()
    {
        $template = $this->template;

        $template->notificationAreas = $this->notification->getNotificationAreaProviders();
        $template->user = $this->user;
        $template->setFile(__DIR__ . '/MenuNotification.latte');
        $template->render();
    }
}