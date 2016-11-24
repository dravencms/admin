<?php

namespace Dravencms\AdminModule\Components\Admin\Breadcrumb;

use Dravencms\Components\BaseControl;
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
        parent::__construct();
        $this->user = $user;
        $this->menuRepository = $menuRepository;
    }

    public function render()
    {
        $template = $this->template;

        $thisPage = $this->menuRepository->getByPresenterAndAction(':'.$this->presenter->getName(), $this->presenter->getAction());

        if (!$thisPage)
        {
            $thisPage = $this->menuRepository->getByPresenter(':'.$this->presenter->getName());
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