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

use RuntimeException;

/**
 * This is the math class.
 *
 * @author Vincent Klaiber <hello@vinkla.com>
 * @author Jakub Kramarz <lenwe@lenwe.net>
 */
class Math
{
    /**
     * Add two arbitrary-length integers.
     *
     * @param string $a
     * @param string $b
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public static function add($a, $b)
    {
        if (function_exists('gmp_add')) {
            return gmp_add($a, $b);
        }

        if (function_exists('bcadd')) {
            return bcadd($a, $b, 0);
        }

        throw new RuntimeException('Missing BC Math or GMP extension.');
    }

    /**
     * Multiply two arbitrary-length integers.
     *
     * @param string $a
     * @param string $b
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public static function multiply($a, $b)
    {
        if (function_exists('gmp_mul')) {
            return gmp_mul($a, $b);
        }

        if (function_exists('bcmul')) {
            return bcmul($a, $b, 0);
        }

        throw new RuntimeException('Missing BC Math or GMP extension.');
    }

    /**
     * Divide two arbitrary-length integers.
     *
     * @param string $a
     * @param string $b
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public static function divide($a, $b)
    {
        if (function_exists('gmp_div_q')) {
            return gmp_div_q($a, $b);
        }

        if (function_exists('bcdiv')) {
            return bcdiv($a, $b, 0);
        }

        throw new RuntimeException('Missing BC Math or GMP extension.');
    }

    /**
     * Compute arbitrary-length integer modulo.
     *
     * @param string $n
     * @param string $d
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public static function mod($n, $d)
    {
        if (function_exists('gmp_mod')) {
            return gmp_mod($n, $d);
        }

        if (function_exists('bcmod')) {
            return bcmod($n, $d);
        }

        throw new RuntimeException('Missing BC Math or GMP extension.');
    }

    /**
     * Compares two arbitrary-length integers.
     *
     * @param string $a
     * @param string $b
     *
     * @throws \RuntimeException
     *
     * @return bool
     */
    public static function greaterThan($a, $b)
    {
        if (function_exists('gmp_cmp')) {
            return gmp_cmp($a, $b) > 0;
        }

        if (function_exists('bccomp')) {
            return bccomp($a, $b, 0) > 0;
        }

        throw new RuntimeException('Missing BC Math or GMP extension.');
    }

    /**
     * Converts arbitrary-length integer to PHP integer.
     *
     * @param string $a
     *
     * @return int
     */
    public static function intval($a)
    {
        if (function_exists('gmp_intval')) {
            return gmp_intval($a);
        }

        return intval($a);
    }

    /**
     * Converts arbitrary-length integer to PHP string.
     *
     * @param string $a
     *
     * @return string
     */
    public static function strval($a)
    {
        if (function_exists('gmp_strval')) {
            return gmp_strval($a);
        }

        return $a;
    }

    /**
     * Converts PHP integer to arbitrary-length integer.
     *
     * @param int $a
     *
     * @return string
     */
    public static function get($a)
    {
        if (function_exists('gmp_init')) {
            return gmp_init($a);
        }

        return $a;
    }
}
