<?php declare(strict_types=1);

namespace Dravencms\AdminModule\Components\Admin\MenuNavbar;

interface MenuNavbarFactory
{
    public function create(): MenuNavbar;
}
