<?php declare(strict_types = 1);

namespace Dravencms\AdminModule;

use Dravencms\AdminModule\Components\Admin\Breadcrumb\Breadcrumb;
use Dravencms\AdminModule\Components\Admin\MenuNavbar\MenuNavbar;
use Dravencms\AdminModule\Components\Admin\MenuNavbar\MenuNavbarFactory;
use Dravencms\AdminModule\Components\Admin\Breadcrumb\BreadcrumbFactory;
use Dravencms\AdminModule\Components\Admin\MenuNotification\MenuNotification;
use Dravencms\AdminModule\Components\Admin\MenuNotification\MenuNotificationFactory;
use Dravencms\User\DefaultDataCreator;
use Dravencms\Database\EntityManager;
use Dravencms\User\TSecuredPresenter;


/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
abstract class SecuredPresenter extends BasePresenter
{
    public static $redirectUnauthorizedTo = ':Admin:User:Sign:In';

    use TSecuredPresenter;

    /** @var MenuNavbarFactory @inject */
    public $menuNavbarFactory;

    /** @var BreadcrumbFactory @inject */
    public $menuBreadcrumbFactory;

    /** @var MenuNotificationFactory @inject */
    public $menuNotificationFactory;

    /** @var EntityManager @inject */
    public $entityManager;

    /** @var DefaultDataCreator @inject */
    public $defaultDataCreator;


    /**
     * @FIXME remove this and use https://github.com/ipublikuj/gravatar when ported to nette3 and releaset
     * @throws \Exception
     */
    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->gravatar = 'https://www.gravatar.com/avatar/'.md5($this->getUserEntity()->getEmail()).'?d=retro';
    }

    /**
     * @return Components\Admin\MenuNavbar\MenuNavbar
     */
    public function createComponentMenu(): MenuNavbar
    {
        return $this->menuNavbarFactory->create($this->getUserEntity());
    }

    /**
     * @return Components\Admin\Breadcrumb\Breadcrumb
     */
    public function createComponentMenuBreadcrumb(): Breadcrumb
    {
        return $this->menuBreadcrumbFactory->create($this->getUserEntity());
    }

    /**
     * @return Components\Admin\MenuNotification\MenuNotification
     */
    public function createComponentMenuNotification(): MenuNotification
    {
        return $this->menuNotificationFactory->create($this->getUserEntity());
    }

}