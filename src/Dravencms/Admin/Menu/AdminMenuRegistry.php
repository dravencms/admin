<?php declare(strict_types=1);

namespace Dravencms\Admin\Menu;

final class AdminMenuRegistry
{
    /** @var list<AdminMenuProviderInterface> */
    private array $providers = [];
    /** @var array<string, AdminMenuItem>|null */
    private ?array $items = null;

    public function __construct(private array $overrides = [], private ?string $homepage = null)
    {
    }

    public function addProvider(AdminMenuProviderInterface $provider): void
    {
        $this->providers[] = $provider;
        $this->items = null;
    }

    /** @return array<string, AdminMenuItem> */
    public function getItems(): array
    {
        if ($this->items !== null) {
            return $this->items;
        }
        $items = [];
        foreach ($this->providers as $provider) {
            foreach ($provider->getItems() as $item) {
                if (!$item instanceof AdminMenuItem) {
                    throw new \InvalidArgumentException('Menu providers must return AdminMenuItem instances.');
                }
                if (isset($items[$item->id])) {
                    throw new \InvalidArgumentException("Duplicate admin menu ID '$item->id'.");
                }
                $items[$item->id] = $item;
            }
        }
        foreach ($this->overrides as $id => $override) {
            if (!isset($items[$id])) {
                throw new \InvalidArgumentException("Unknown admin menu override '$id'.");
            }
            $items[$id] = $items[$id]->withOverrides($override);
        }
        foreach ($items as $item) {
            $seen = [$item->id => true];
            $parent = $item->parent;
            while ($parent !== null) {
                if (!isset($items[$parent])) {
                    throw new \InvalidArgumentException("Missing parent '$parent' for menu item '$item->id'.");
                }
                if (isset($seen[$parent])) {
                    throw new \InvalidArgumentException("Cycle in admin menu at '$parent'.");
                }
                $seen[$parent] = true;
                $parent = $items[$parent]->parent;
            }
        }
        if ($this->homepage !== null && (!isset($items[$this->homepage]) || $items[$this->homepage]->destination === null)) {
            throw new \InvalidArgumentException("Admin menu homepage '$this->homepage' must identify a link.");
        }
        uasort($items, static fn(AdminMenuItem $a, AdminMenuItem $b): int => [$a->order, $a->id] <=> [$b->order, $b->id]);
        return $this->items = $items;
    }

    /** Permission decisions are made for each request, never cached with the definitions.
     * @param callable(string, ?string): bool $isAllowed
     * @return array<string, AdminMenuItem>
     */
    public function getVisibleItems(callable $isAllowed): array
    {
        $items = $this->getItems();
        $visible = [];
        $visit = function (AdminMenuItem $item) use (&$visit, &$visible, $items, $isAllowed): bool {
            if (!$item->enabled || ($item->resource !== null && !$isAllowed($item->resource, $item->privilege))) {
                return false;
            }
            $hasChildren = false;
            foreach ($items as $child) {
                if ($child->parent === $item->id && $visit($child)) {
                    $hasChildren = true;
                }
            }
            if ($item->destination === null && !$hasChildren) {
                return false;
            }
            $visible[$item->id] = $item;
            return true;
        };
        foreach ($items as $item) {
            if ($item->parent === null) {
                $visit($item);
            }
        }
        return array_intersect_key($items, $visible);
    }

    /** @param array<string, AdminMenuItem> $items */
    public function getActiveItem(array $items, string $presenter, string $action, array $parameters = []): ?AdminMenuItem
    {
        $current = ltrim($presenter, ':') . ':' . $action;
        $best = null;
        $bestScore = 0;
        foreach ($items as $item) {
            if ($item->destination === null) {
                continue;
            }
            foreach ($item->parameters as $key => $value) {
                if (!array_key_exists($key, $parameters) || $parameters[$key] != $value) {
                    continue 2;
                }
            }
            $destination = ltrim($item->destination, ':');
            $targetPresenter = substr($destination, 0, strrpos($destination, ':'));
            $score = strcasecmp($destination, $current) === 0 ? 1000 : (strcasecmp($targetPresenter, ltrim($presenter, ':')) === 0 ? 100 : 0);
            foreach ($item->activeDestinations as $pattern) {
                if (fnmatch(ltrim($pattern, ':'), $current, FNM_CASEFOLD)) {
                    $score = max($score, 500);
                }
            }
            if ($score > 0) {
                $score += count($item->parameters);
                if ($score > $bestScore) {
                    $best = $item;
                    $bestScore = $score;
                }
            }
        }
        return $best;
    }

    /** @param array<string, AdminMenuItem> $items @return list<AdminMenuItem> */
    public function getBreadcrumbs(array $items, ?AdminMenuItem $active): array
    {
        $result = [];
        while ($active !== null) {
            array_unshift($result, $active);
            $active = $active->parent !== null ? ($items[$active->parent] ?? null) : null;
        }
        return $result;
    }

    /** @return list<array{item: AdminMenuItem, children: array, active: bool}> */
    public function getTree(callable $isAllowed, string $presenter = '', string $action = 'default', array $parameters = []): array
    {
        $items = $this->getVisibleItems($isAllowed);
        $active = $this->getActiveItem($items, $presenter, $action, $parameters);
        $activeIds = array_column($this->getBreadcrumbs($items, $active), 'id');
        $build = function (?string $parent) use (&$build, $items, $activeIds): array {
            $tree = [];
            foreach ($items as $item) {
                if ($item->parent === $parent) {
                    $tree[] = ['item' => $item, 'children' => $build($item->id), 'active' => in_array($item->id, $activeIds, true)];
                }
            }
            return $tree;
        };
        return $build(null);
    }

    public function getHomePage(callable $isAllowed): ?AdminMenuItem
    {
        $items = $this->getVisibleItems($isAllowed);
        if ($this->homepage !== null && isset($items[$this->homepage])) {
            return $items[$this->homepage];
        }
        foreach ($items as $item) {
            if ($item->isHomePage) {
                return $item;
            }
        }
        return null;
    }
}
