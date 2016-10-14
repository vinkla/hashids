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
            return gmp_intval(gmp_add($a, $b));
        }

        return intval(bcadd($a, $b));
    }

    /**
     * Divide two arbitrary precision numbers.
     *
     * @param int $a
     * @param int $b
     *
     * @return int
     */
    public static function divide($a, $b)
    {
        if (function_exists('gmp_div_q')) {
            return gmp_intval(gmp_div_q($a, $b));
        }

        return intval(bcdiv($a, $b));
    }

    /**
     * Raise number into power.
     *
     * @param int $base
     * @param int $exp
     *
     * @return int
     */
    public static function pow($base, $exp)
    {
        if (function_exists('gmp_pow')) {
            return gmp_intval(gmp_pow($base, $exp));
        }

        return pow($base, $exp);
    }
}
