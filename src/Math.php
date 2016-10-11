<?php

/*
 * This file is part of Hashids.
 *
 * (c) Ivan Akimov <ivan@barreleye.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hashids;

/**
 * This is the math class.
 *
 * @author Vincent Klaiber <hello@vinkla.com>
 */
class Math
{
    /**
     * Add two arbitrary precision numbers.
     *
     * @param int $a
     * @param int $b
     *
     * @return int
     */
    public static function add($a, $b)
    {
        if (function_exists('gmp_add')) {
            return gmp_add($a, $b);
        }

        if (function_exists('bcadd')) {
            return bcadd($a, $b);
        }
    }

    /**
     * Divide two arbitrary precision numbers.
     *
     * @param int $a
     * @param int $b
     *
     * @return int
     */
    public static function div($a, $b)
    {
        if (function_exists('gmp_div_q')) {
            return gmp_div_q($a, $b);
        }

        if (function_exists('bcdiv')) {
            return bcdiv($a, $b);
        }
    }
}
