<?php declare(strict_types=1);

namespace Dravencms\AdminModule\Components\Admin\Breadcrumb;

use Dravencms\Admin\Menu\AdminMenuRegistry;
use Dravencms\Components\BaseControl\BaseControl;
use Nette\Security\User;

class Breadcrumb extends BaseControl
{
    public function __construct(private AdminMenuRegistry $menu, private User $user)
    {
    }

    public function render(): void
    {
        $presenter = $this->getPresenter();
        $allowed = fn(string $resource, ?string $privilege): bool => $this->user->isAllowed($resource, $privilege);
        $items = $this->menu->getVisibleItems($allowed);
        $active = $this->menu->getActiveItem($items, $presenter->getName(), $presenter->getAction(), $presenter->getParameters());
        $this->template->breadcrumbs = $this->menu->getBreadcrumbs($items, $active);
        $this->template->homepage = $this->menu->getHomePage($allowed);
        $this->template->setFile(__DIR__ . '/Breadcrumb.latte');
        $this->template->render();
    }
}
