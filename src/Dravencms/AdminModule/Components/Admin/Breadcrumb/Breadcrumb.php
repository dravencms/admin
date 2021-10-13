<?php declare(strict_types = 1);

namespace Dravencms\AdminModule\Components\Admin\Breadcrumb;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Model\Admin\Repository\MenuRepository;
use Dravencms\Model\User\Entities\User;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class Breadcrumb extends BaseControl
{
    /** @var MenuRepository */
    private $menuRepository;

    /** @var User */
    private $user;

    public function __construct(User $user, MenuRepository $menuRepository)
    {
        $this->user = $user;
        $this->menuRepository = $menuRepository;
    }

    public function render(): void
    {
        $template = $this->template;

        $thisPage = $this->menuRepository->getByPresenterAndAction(':'.$this->presenter->getName(), $this->presenter->getAction());

        if (!$thisPage)
        {
            $thisPage = $this->menuRepository->getOneByPresenter(':'.$this->presenter->getName());
        }

        $template->thisPage = $thisPage;
        if ($thisPage)
        {
            $template->breadcrumbs = $this->menuRepository->buildParentTree($thisPage);
        }
        else
        {
            $template->breadcrumbs = [];
        }

        $template->homepage = $this->menuRepository->getHomePageForUser($this->user);

        $template->setFile(__DIR__.'/Breadcrumb.latte');
        $template->render();
    }
}