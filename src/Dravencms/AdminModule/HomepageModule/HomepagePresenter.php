<?php declare(strict_types=1);
namespace Dravencms\AdminModule\HomepageModule;

use Dravencms\Admin\Dashboard\DashboardRegistry;
use Dravencms\AdminModule\SecuredPresenter;
use Nette\Application\UI\Multiplier;

class HomepagePresenter extends SecuredPresenter
{
    /** @inject */
    public DashboardRegistry $dashboardRegistry;

    public function renderDefault(): void
    {
        $this->template->dashboardPanels = $this->dashboardRegistry->getVisiblePanels($this->getUser());
    }

    protected function createComponentDashboard(): Multiplier
    {
        return new Multiplier(fn(string $name) => $this->dashboardRegistry->getComponent($name, $this->getUser()));
    }
}
