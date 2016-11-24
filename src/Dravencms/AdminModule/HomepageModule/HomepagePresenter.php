<?php

namespace Dravencms\AdminModule\HomepageModule;

use Dravencms\AdminModule\SecuredPresenter;
use Nette;
use Salamek\Cms\TCms;
use Tracy\Debugger;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends SecuredPresenter
{
    use TCms;

    public function renderDefault()
    {
        $this->template->tree = Debugger::$productionMode ? null : $this->cms->getTree();
    }
}
