<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Admin\Filters;


use Nette\Utils\Html;

class Latte
{
    /**
     * @param $number
     * @param bool $separated
     * @return static
     */
    public function formatCounter($number, $separated = false)
    {
        if ($separated) {
            $root = Html::el('div');
            $root->class = 'counter-separated counter-lg';
            foreach (str_split($number) AS $piece) {
                $p = Html::el('span', $piece);
                $root->addHtml($p);
            }
        } else {
            $root = Html::el('div');
            $root->class = 'counter counter-lg';
            $p = Html::el('span', $number);
            $root->addHtml($p);
        }
        return $root;
    }
}