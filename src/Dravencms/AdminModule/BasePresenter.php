<?php

namespace Dravencms\AdminModule;

use Dravencms\AdminModule\Components\Admin\MenuNavbar\MenuNavbarFactory;
use Dravencms\AdminModule\Components\Admin\Breadcrumb\BreadcrumbFactory;
use Dravencms\AdminModule\Components\Admin\MenuNotification\MenuNotificationFactory;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends \Dravencms\BasePresenter
{
    /** @var MenuNavbarFactory @inject */
    public $menuNavbarFactory;

    /** @var BreadcrumbFactory @inject */
    public $menuBreadcrumbFactory;

    /** @var MenuNotificationFactory @inject */
    public $menuNotificationFactory;

    /**
     * Checks authorization.
     * @return void
     */
    public function checkRequirements($element)
    {
        parent::checkRequirements($element);

        $this->getUser()->getStorage()->setNamespace('Admin');
    }

    /**
     * @return \WebLoader\Nette\CssLoader
     */
    public function createComponentCss()
    {
        return $this->webLoader->createCssLoader('admin');
    }

    /**
     * @return \WebLoader\Nette\JavaScriptLoader
     */
    public function createComponentJs()
    {
        return $this->webLoader->createJavaScriptLoader('admin');
    }

    /**
     * @return Components\Admin\MenuNavbar\MenuNavbar
     */
    public function createComponentMenu()
    {
        return $this->menuNavbarFactory->create($this->getUserEntity());
    }

    /**
     * @return Components\Admin\Breadcrumb\Breadcrumb
     */
    public function createComponentMenuBreadcrumb()
    {
        return $this->menuBreadcrumbFactory->create($this->getUserEntity());
    }

    /**
     * @return Components\Admin\MenuNotification\MenuNotification
     */
    public function createComponentMenuNotification()
    {
        return $this->menuNotificationFactory->create($this->getUserEntity());
    }

    /**
     * Formats layout template file names.
     * @return array
     */
    public function formatLayoutTemplateFiles()
    {
        $name = $this->getName();
        $presenter = substr($name, strrpos(':' . $name, ':'));
        $className = trim(str_replace($presenter . 'Presenter', '', get_class($this)), '\\');
        $exploded = explode('\\', $className);
        $moduleName = str_replace('Module', '', end($exploded));
        $layout = $this->layout ? $this->layout : 'layout';
        $dir = dirname($this->getReflection()->getFileName());
        $dir = is_dir("$dir/templates") ? $dir : dirname($dir);
        $list = array(
            "$dir/templates/$moduleName/$presenter/@$layout.latte",
            "$dir/templates/$moduleName/$presenter.@$layout.latte",
        );
        do {
            $list[] = "$dir/templates/@$layout.latte";
            $dir = dirname($dir);
        } while ($dir && ($name = substr($name, 0, strrpos($name, ':'))));

        $list[] = __DIR__."/templates/@$layout.latte";
        
        return $list;
    }
}
