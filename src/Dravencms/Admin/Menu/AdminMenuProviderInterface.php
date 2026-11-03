<?php declare(strict_types=1);

namespace Dravencms\Admin\Menu;

interface AdminMenuProviderInterface
{
    /** @return iterable<AdminMenuItem> */
    public function getItems(): iterable;
}
