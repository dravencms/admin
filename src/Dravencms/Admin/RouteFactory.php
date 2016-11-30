<?php

namespace Dravencms\Admin;

use Dravencms\Base\IRouterFactory;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class RouteFactory implements IRouterFactory
{
    /**
     * @return \Nette\Application\IRouter
     */
    public function createRouter()
    {
        $router = new RouteList();


        $router[] = $admin = new RouteList('Admin');
        $admin[] = new Route('admin/index.php', 'Homepage:Homepage:default', Route::ONE_WAY);
        $admin[] = new Route('admin/[<locale [a-z]{2}>/]<presenter>/<action>[/<id [0-9]+>]', 'Homepage:Homepage:default');

        return $router;
    }
}