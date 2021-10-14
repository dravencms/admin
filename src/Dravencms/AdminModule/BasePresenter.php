<?php declare(strict_types = 1);

namespace Dravencms\AdminModule;

use Dravencms\Locale\TLocalizedPresenter;
use Dravencms\User\TUserPresenter;
use WebLoader\Nette\CssLoader;
use WebLoader\Nette\JavaScriptLoader;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends \Dravencms\BasePresenter
{
    use TLocalizedPresenter;
    use TUserPresenter;

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
     * Formats layout template file names.
     * @return array
     */
    public function formatLayoutTemplateFiles(): array
    {
        $list = parent::formatLayoutTemplateFiles();
        $layout = $this->getLayoutName();
        $list[] = __DIR__."/templates/@$layout.latte";
        return $list;
    }
}
