<?php declare(strict_types=1);
namespace Dravencms\Admin\Dashboard;

use Nette\Application\BadRequestException;
use Nette\Application\UI\Control;
use Nette\Security\User;

final class DashboardRegistry
{
    /** @var array<string, array{DashboardDefinition, callable(): (Control&IDashboardComponent)}> */
    private array $panels = [];

    public function addFactory(callable $factory): void
    {
        $component = $factory();
        if (!$component instanceof Control || !$component instanceof IDashboardComponent) { throw new \InvalidArgumentException('Dashboard components must be Nette UI controls.'); }
        $definition = $component->getDefinition();
        $name = $definition->getControlName();
        if (isset($this->panels[$name])) { throw new \InvalidArgumentException('Duplicate dashboard panel ID: ' . $definition->id); }
        $this->panels[$name] = [$definition, $factory];
    }

    /** @return array<string, DashboardDefinition> */
    public function getVisiblePanels(User $user): array
    {
        $visible = [];
        foreach ($this->panels as $name => [$definition]) {
            if ($this->isAllowed($definition, $user)) { $visible[$name] = $definition; }
        }
        uasort($visible, static fn(DashboardDefinition $a, DashboardDefinition $b): int => [$a->order, $a->id] <=> [$b->order, $b->id]);
        return $visible;
    }

    public function getComponent(string $name, User $user): Control
    {
        // Recheck on component access, including forged links/signals to hidden panels.
        if (!isset($this->panels[$name]) || !$this->isAllowed($this->panels[$name][0], $user)) { throw new BadRequestException('Dashboard panel not available.', 404); }
        $component = ($this->panels[$name][1])();
        if (!$component instanceof Control || !$component instanceof IDashboardComponent || $component->getDefinition() != $this->panels[$name][0]) {
            throw new \LogicException('Dashboard factories must return controls with stable metadata.');
        }
        return $component;
    }

    private function isAllowed(DashboardDefinition $definition, User $user): bool
    {
        return $user->isLoggedIn() && ($definition->resource === null || $user->isAllowed($definition->resource, $definition->privilege));
    }
}
