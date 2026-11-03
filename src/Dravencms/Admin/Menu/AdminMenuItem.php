<?php declare(strict_types=1);

namespace Dravencms\Admin\Menu;

final readonly class AdminMenuItem
{
    /** @param array<string, mixed> $parameters @param list<string> $activeDestinations */
    public function __construct(
        public string $id,
        public string $label,
        public ?string $destination = null,
        public ?string $parent = null,
        public string $icon = 'fa-circle',
        public int $order = 100,
        public ?string $resource = null,
        public ?string $privilege = null,
        public array $parameters = [],
        public array $activeDestinations = [],
        public bool $enabled = true,
        public bool $isHomePage = false,
    ) {
        if ($id === '' || $label === '') {
            throw new \InvalidArgumentException('Menu item ID and translation key must not be empty.');
        }
        if ($privilege !== null && $resource === null) {
            throw new \InvalidArgumentException("Menu item '$id' specifies a privilege without a resource.");
        }
        if ($isHomePage && $destination === null) {
            throw new \InvalidArgumentException("Menu homepage '$id' must have a destination.");
        }
    }

    public function withOverrides(array $overrides): self
    {
        foreach ($overrides as $key => $value) {
            if (!in_array($key, ['enabled', 'order', 'parent'], true)) {
                throw new \InvalidArgumentException("Unsupported menu override '$key' for '$this->id'.");
            }
        }
        return new self(
            $this->id, $this->label, $this->destination,
            array_key_exists('parent', $overrides) ? $overrides['parent'] : $this->parent,
            $this->icon, $overrides['order'] ?? $this->order, $this->resource, $this->privilege,
            $this->parameters, $this->activeDestinations, $overrides['enabled'] ?? $this->enabled, $this->isHomePage,
        );
    }
}
