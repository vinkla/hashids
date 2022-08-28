<?php

/**
 * Copyright (c) Ivan Akimov.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/vinkla/hashids
 */

namespace Hashids\Math;

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
