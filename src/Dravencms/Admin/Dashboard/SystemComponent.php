<?php declare(strict_types=1);
namespace Dravencms\Admin\Dashboard;

final class SystemComponent extends StatisticsComponent
{
    public function getDefinition(): DashboardDefinition
    {
        return new DashboardDefinition('admin.system', 'admin.presenter.homepage.SystemInfo', 1000, icon: 'fa-server');
    }

    protected function getMetrics(): iterable
    {
        yield new DashboardMetric('admin.dashboard.php', PHP_VERSION);
        if (isset($_SERVER['SERVER_SOFTWARE'])) { yield new DashboardMetric('admin.presenter.homepage.server', (string) $_SERVER['SERVER_SOFTWARE']); }
    }
}
