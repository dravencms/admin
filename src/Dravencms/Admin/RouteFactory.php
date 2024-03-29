<?php declare(strict_types = 1);

namespace Dravencms\Admin;


use Dravencms\Application\IRouterFactory;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class RouteFactory implements IRouterFactory
{
    /**
     * @return array|RouteList
     */
    public function createRouter(): RouteList
    {
        $router = new RouteList();

        $admin = new RouteList('Admin');
        $admin->addRoute('admin/index.php', 'Homepage:Homepage:default', Route::ONE_WAY);
        $admin->addRoute('admin/[<locale [a-z]{2}>/]<presenter>/<action>[/<id [0-9]+>]', 'Homepage:Homepage:default');

        $router->add($admin);

        return $router;
    }
}