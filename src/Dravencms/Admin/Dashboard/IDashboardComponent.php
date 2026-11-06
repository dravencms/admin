<?php declare(strict_types=1);
namespace Dravencms\Admin\Dashboard;

/** Implement on a Nette UI Control; metadata and constructors must not load panel data. */
interface IDashboardComponent
{
    public function getDefinition(): DashboardDefinition;
    public function render(): void;
}
