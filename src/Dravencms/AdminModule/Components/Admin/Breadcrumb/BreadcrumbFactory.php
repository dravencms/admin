<?php declare(strict_types=1);

namespace Dravencms\AdminModule\Components\Admin\Breadcrumb;

interface BreadcrumbFactory
{
    public function create(): Breadcrumb;
}
