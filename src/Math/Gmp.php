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
 * This is the Gmp math class.
 *
 * @author Vincent Klaiber <hello@vinkla.com>
 * @author Jakub Kramarz <lenwe@lenwe.net>
 */
class Gmp implements MathInterface
{
    public function add($a, $b)
    {
        return gmp_add($a, $b);
    }

    public function multiply($a, $b)
    {
        return gmp_mul($a, $b);
    }

    public function divide($a, $b)
    {
        return gmp_div_q($a, $b);
    }

    public function mod($n, $d)
    {
        return gmp_mod($n, $d);
    }

    public function greaterThan($a, $b)
    {
        return gmp_cmp($a, $b) > 0;
    }

    public function intval($a)
    {
        return gmp_intval($a);
    }

    public function strval($a)
    {
        return gmp_strval($a);
    }

    public function get($a)
    {
        return gmp_init($a);
    }
}
