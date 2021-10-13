<?php declare(strict_types = 1);
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Admin\Filters;


use Nette\Utils\Html;

class Latte
{
    /**
     * @param int $number
     * @param bool $separated
     * @return Html
     */
    public function formatCounter(int $number, bool $separated = false): Html
    {
        if ($separated) {
            $root = Html::el('div');
            $root->class = 'counter-separated counter-lg';
            foreach (str_split(strval($number)) AS $piece) {
                $p = Html::el('span', $piece);
                $root->addHtml($p);
            }
            return $root;
        }

        $root = Html::el('div');
        $root->class = 'counter counter-lg';
        $p = Html::el('span', $number);
        $root->addHtml($p);

        return $root;
    }
}