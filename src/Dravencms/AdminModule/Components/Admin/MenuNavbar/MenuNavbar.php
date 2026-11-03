<?php declare(strict_types=1);

namespace Dravencms\AdminModule\Components\Admin\MenuNavbar;

use Dravencms\Admin\Menu\AdminMenuRegistry;
use Dravencms\Components\BaseControl\BaseControl;
use Nette\Security\User;

class MenuNavbar extends BaseControl
{
    public function __construct(private AdminMenuRegistry $menu, private User $user)
    {
    }

    public function render(): void
    {
        $presenter = $this->getPresenter();
        $allowed = fn(string $resource, ?string $privilege): bool => $this->user->isAllowed($resource, $privilege);
        $this->template->nodes = $this->menu->getTree($allowed, $presenter->getName(), $presenter->getAction(), $presenter->getParameters());
        $this->template->setFile(__DIR__ . '/MenuNavbar.latte');
        $this->template->render();
    }
}
