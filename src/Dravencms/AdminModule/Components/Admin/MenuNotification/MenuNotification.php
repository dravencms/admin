<?php

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */


namespace Dravencms\AdminModule\Components\Admin\MenuNotification;

use Dravencms\Components\BaseControl\BaseControl;

use Dravencms\Model\Admin\Repository\MenuRepository;
use Dravencms\Model\Admin\Repository\NotificationRepository;
use Dravencms\Model\User\Entities\User;

class MenuNotification extends BaseControl
{
    /** @var User */
    private $user;

    /** @var NotificationRepository */
    private $notificationRepository;

    /**
     * MenuNavbar constructor.
     * @param User $user
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(User $user, NotificationRepository $notificationRepository)
    {
        parent::__construct();

        $this->user = $user;
        $this->notificationRepository = $notificationRepository;
    }


    public function render()
    {
        $template = $this->template;

        $template->notifications = $this->notificationRepository->getForUser($this->user);

        $template->setFile(__DIR__ . '/MenuNotification.latte');
        $template->render();
    }
}