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
        
        $isActive = function (array $node) use (&$isActive): bool {
            foreach ($node['__children'] ?? [] as $child) {
                if ($isActive($child)) return true;
            }
            return empty($node['__children']) && $this->getPresenter()->isLinkCurrent($node['presenter'].':'.$node['action'], $node['parameters'] ?? []);
        };
        $options = [
            'decorate' => true,
            'rootOpen' => function ($tree) {
                if (count($tree) && ($tree[0]['lvl'] == 0)) {
                    return '<ul class="navbar-nav pt-lg-3">';
                } else {
                    return '<ul class="navbar-nav ms-3">';
                }
            },
            'rootClose' => function ($tree) {
                if (count($tree) && ($tree[0]['lvl'] == 0)) {
                    return '</ul>';
                } else {
                    return '</ul>';
                }
            },
            'childOpen' => function ($tree) use ($isActive) {
                $active = $isActive($tree);
                return '<li class="nav-item' . ($active ? ' active' : '') . '">' . (!empty($tree['__children']) ? '<details'.($active ? ' open' : '').'>' : '');
            },
            'childClose' => static fn(array $node): string => (!empty($node['__children']) ? '</details>' : '').'</li>',
            'nodeDecorator' => function ($node) {
                $group = !empty($node['__children']);
                $link = \Nette\Utils\Html::el($group ? 'summary' : 'a', ['class' => 'nav-link']);
                if (!$group) $link->href($this->getPresenter()->link($node['presenter'].':'.$node['action'], $node['parameters'] ?? []));
                $link->addHtml(\Nette\Utils\Html::el('span', ['class' => 'nav-link-icon'])->addHtml(\Nette\Utils\Html::el('i', ['class' => 'fa '.$node['icon']])));
                $link->addHtml(\Nette\Utils\Html::el('span', ['class' => 'nav-link-title'])->setText($node['name']));
                if ($group) $link->addHtml(\Nette\Utils\Html::el('i', ['class' => 'fa fa-angle-down ms-auto']));
                return (string) $link;
            }
        ];

        $template->htmlTree = $this->menuRepository->getHtmlTreeForUser($options, $this->user);

        $template->setFile(__DIR__ . '/MenuNavbar.latte');
        $template->render();
    }
}
