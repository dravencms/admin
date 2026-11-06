<?php declare(strict_types=1);
namespace Dravencms\Admin\Dashboard;

use Dravencms\Components\BaseControl\BaseControl;

/** Optional shared renderer; custom panels can implement IDashboardComponent directly. */
abstract class StatisticsComponent extends BaseControl implements IDashboardComponent
{
    /** @return iterable<DashboardMetric> */
    abstract protected function getMetrics(): iterable;

    public function render(): void
    {
        $definition = $this->getDefinition();
        $user = $this->getPresenter()->getUser();
        if (!$user->isLoggedIn() || ($definition->resource !== null && !$user->isAllowed($definition->resource, $definition->privilege))) {
            throw new \Nette\Application\BadRequestException('Dashboard panel not available.', 404);
        }
        // Queries run only for a visible panel, when its body is rendered.
        $this->template->metrics = $this->getMetrics();
        $this->template->setFile(__DIR__ . '/statistics.latte');
        $this->template->render();
    }
}
