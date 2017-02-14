<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\AdminModule\Components\Admin\MenuNotification;

use Dravencms\Model\User\Entities\User;

interface MenuNotificationFactory
{
    /**
     * @param User $user
     * @return MenuNotification
     */
    public function create(User $user);
}