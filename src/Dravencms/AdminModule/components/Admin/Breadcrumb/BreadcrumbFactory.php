<?php

namespace Dravencms\AdminModule\Components\Admin;

use Dravencms\Model\User\Entities\User;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
interface BreadcrumbFactory
{
    /**
     * @param User $user
     * @return Breadcrumb
     */
    public function create(User $user);
}