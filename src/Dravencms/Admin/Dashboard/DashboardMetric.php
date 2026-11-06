<?php declare(strict_types=1);
namespace Dravencms\Admin\Dashboard;

final readonly class DashboardMetric
{
    public function __construct(public string $label, public int|string $value, public bool $bytes = false) {}
}
