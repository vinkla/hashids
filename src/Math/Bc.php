<?php

/*
 * This file is part of Hashids.
 *
 * (c) Ivan Akimov <ivan@barreleye.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hashids\Math;

/**
 * This is the Bc math class.
 *
 * @author Vincent Klaiber <hello@vinkla.com>
 * @author Jakub Kramarz <lenwe@lenwe.net>
 */
class Bc implements MathInterface
{
    public function add($a, $b)
    {
        return bcadd($a, $b, 0);
    }

    public function multiply($a, $b)
    {
        return bcmul($a, $b, 0);
    }

    public function divide($a, $b)
    {
        return bcdiv($a, $b, 0);
    }

    public function mod($n, $d)
    {
        return bcmod($n, $d);
    }

    public function greaterThan($a, $b)
    {
        return bccomp($a, $b, 0) > 0;
    }

    public function intval($a)
    {
        return intval($a);
    }

    public function strval($a)
    {
        return $a;
    }

    public function get($a)
    {
        return $a;
    }
}
