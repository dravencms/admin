<?php declare(strict_types = 1);

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */


namespace Dravencms\AdminModule\Components\Admin\MenuNavbar;

use Dravencms\Components\BaseControl\BaseControl;

use Dravencms\Model\Admin\Repository\MenuRepository;
use Dravencms\Model\User\Entities\User;

class MenuNavbar extends BaseControl
{
    /**
     * @var User
     */
    private $user;

    /** @var MenuRepository */
    private $menuRepository;

    /**
     * MenuNavbar constructor.
     * @param User $user
     * @param MenuRepository $menuRepository
     */
    public function __construct(User $user, MenuRepository $menuRepository)
    {
        $this->user = $user;
        $this->menuRepository = $menuRepository;
    }


    public function render(): void
    {
        $template = $this->template;
        
        $options = [
            'decorate' => true,
            'rootOpen' => function ($tree) {
                if (count($tree) && ($tree[0]['lvl'] == 0)) {
                    return '<ul class="navigation">';
                } else {
                    return '<ul id="menu-item-'.$tree[0]['lvl'].'">';
                }
            },
            'rootClose' => function ($tree) {
                if (count($tree) && ($tree[0]['lvl'] == 0)) {
                    return '</ul>';
                } else {
                    return '</ul>';
                }
            },
            'childOpen' => function ($tree) {
                $active = false;
                if (array_key_exists('__children', $tree) && count($tree['__children'])) {
                    foreach ($tree['__children'] AS $child) {
                        if ($this->presenter->isLinkCurrent($child['presenter'].':'.$child['action'], $node['parameters'] ?? [])) {
                            $active = true;
                        }
                    }
                } else {
                    $active = $this->presenter->isLinkCurrent($tree['presenter'].':'.$tree['action'], $node['parameters'] ?? []);
                }

                return '<li class="' . ($active ? 'active ' : '') . (!empty($tree['__children']) ? 'mm-dropdown mm-dropdown-root': '') . '">';
            },
            'childClose' => '</li>',
            'nodeDecorator' => function ($node) {
                return '<a href="' . (!empty($node['__children']) ? '#' : $this->presenter->link($node['presenter'].':'.$node['action'], $node['parameters'])) . '" '.(!empty($node['__children']) ? ' class="dropdown-toggle" data-toggle="dropdown" data-target="#menu-item-'.$node['id'].'"' : '').'><i class="menu-icon fa '.$node['icon'] .'"></i><span class="mm-text">' . $node['name'] . '</span></a>';
            }
        ];

        $template->htmlTree = $this->menuRepository->getHtmlTreeForUser($options, $this->user);

        $template->setFile(__DIR__ . '/MenuNavbar.latte');
        $template->render();
    }
}