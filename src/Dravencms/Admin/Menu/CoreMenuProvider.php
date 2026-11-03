<?php declare(strict_types=1);

namespace Dravencms\Admin\Menu;

final class CoreMenuProvider implements AdminMenuProviderInterface
{
    public function getItems(): iterable
    {
        yield new AdminMenuItem('admin.home', 'admin.menu.dashboard', ':Admin:Homepage:Homepage:default', icon: 'fa-home', order: 0, isHomePage: true);
        yield new AdminMenuItem('content', 'admin.menu.content', icon: 'fa-cubes', order: 200);
    }
}
