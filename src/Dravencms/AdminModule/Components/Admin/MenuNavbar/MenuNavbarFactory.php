<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\AdminModule\Components\Admin\MenuNavbar;

use Dravencms\Model\User\Entities\User;

interface MenuNavbarFactory
{
    /**
     * @param User $user
     * @return MenuNavbar
     */
    public function create(User $user);
}