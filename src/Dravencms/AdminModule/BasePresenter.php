<?php

namespace Dravencms\AdminModule;

use Dravencms\GlobalPresenter;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends GlobalPresenter
{
    /**
     * Checks authorization.
     * @return void
     */
    public function checkRequirements($element)
    {
        parent::checkRequirements($element);

        $this->getUser()->getStorage()->setNamespace('Admin');
    }

    public function createComponentCss()
    {
        return $this->webLoader->createCssLoader('admin');
    }

    public function createComponentJs()
    {
        return $this->webLoader->createJavaScriptLoader('admin');
    }
}
