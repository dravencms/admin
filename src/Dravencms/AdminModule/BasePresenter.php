<?php declare(strict_types = 1);

namespace Dravencms\AdminModule;

use Dravencms\AdminModule\Components\Admin\Breadcrumb\Breadcrumb;
use Dravencms\AdminModule\Components\Admin\MenuNavbar\MenuNavbar;
use Dravencms\AdminModule\Components\Admin\MenuNavbar\MenuNavbarFactory;
use Dravencms\AdminModule\Components\Admin\Breadcrumb\BreadcrumbFactory;
use Dravencms\AdminModule\Components\Admin\MenuNotification\MenuNotification;
use Dravencms\AdminModule\Components\Admin\MenuNotification\MenuNotificationFactory;
use Dravencms\Locale\TLocalizedPresenter;
use WebLoader\Nette\CssLoader;
use WebLoader\Nette\JavaScriptLoader;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends \Dravencms\BasePresenter
{
    use TLocalizedPresenter;
    
    /** @var MenuNavbarFactory @inject */
    public $menuNavbarFactory;

    /** @var BreadcrumbFactory @inject */
    public $menuBreadcrumbFactory;

    /** @var MenuNotificationFactory @inject */
    public $menuNotificationFactory;

    /**
     * @param $element
     */
    public function checkRequirements($element): void
    {
        parent::checkRequirements($element);

        $this->getUser()->getStorage()->setNamespace('Admin');
    }

    /**
     * @return \WebLoader\Nette\CssLoader
     */
    public function createComponentCss(): CssLoader
    {
        return $this->webLoader->createCssLoader('admin');
    }

    /**
     * @return \WebLoader\Nette\JavaScriptLoader
     */
    public function createComponentJs(): JavaScriptLoader
    {
        return $this->webLoader->createJavaScriptLoader('admin');
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

    /**
     * Formats layout template file names.
     * @return array
     */
    public function formatLayoutTemplateFiles(): array
    {
        $name = $this->getName();
        $presenter = substr($name, strrpos(':' . $name, ':'));
        $className = trim(str_replace($presenter . 'Presenter', '', get_class($this)), '\\');
        $exploded = explode('\\', $className);
        $moduleName = str_replace('Module', '', end($exploded));
        $layout = $this->layout ? $this->layout : 'layout';
        $dir = dirname($this->getReflection()->getFileName());
        $dir = is_dir("$dir/templates") ? $dir : dirname($dir);
        $list = [
            "$dir/templates/$moduleName/$presenter/@$layout.latte",
            "$dir/templates/$moduleName/$presenter.@$layout.latte",
        ];
        do {
            $list[] = "$dir/templates/@$layout.latte";
            $dir = dirname($dir);
        } while ($dir && ($name = substr($name, 0, strrpos($name, ':'))));

        $list[] = __DIR__."/templates/@$layout.latte";
        
        return $list;
    }
}
