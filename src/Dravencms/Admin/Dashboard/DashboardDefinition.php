<?php declare(strict_types=1);
namespace Dravencms\Admin\Dashboard;

final readonly class DashboardDefinition
{
    public function __construct(public string $id, public string $title, public int $order = 100,
        public ?string $resource = null, public ?string $privilege = null,
        public ?string $destination = null, public string $icon = 'fa-info-circle')
    {
        if (!preg_match('/^[a-z][a-z0-9]*(?:[.-][a-z0-9]+)*$/D', $id)) { throw new \InvalidArgumentException('Invalid dashboard panel ID: ' . $id); }
        if ($title === '' || ($privilege !== null && $resource === null)) { throw new \InvalidArgumentException('Invalid dashboard panel metadata: ' . $id); }
    }

    public function getControlName(): string { return bin2hex($this->id); }
}
