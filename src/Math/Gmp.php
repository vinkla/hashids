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
    /**
     * Add two arbitrary-length integers.
     *
     * @param string $a
     * @param string $b
     *
     * @return string
     */
    public function add($a, $b)
    {
        return \gmp_add($a, $b);
    }

    /**
     * Multiply two arbitrary-length integers.
     *
     * @param string $a
     * @param string $b
     *
     * @return string
     */
    public function multiply($a, $b)
    {
        return \gmp_mul($a, $b);
    }

    /**
     * Divide two arbitrary-length integers.
     *
     * @param string $a
     * @param string $b
     *
     * @return string
     */
    public function divide($a, $b)
    {
        return \gmp_div_q($a, $b);
    }

    /**
     * Compute arbitrary-length integer modulo.
     *
     * @param string $n
     * @param string $d
     *
     * @return string
     */
    public function mod($n, $d)
    {
        return \gmp_mod($n, $d);
    }

    /**
     * Compares two arbitrary-length integers.
     *
     * @param string $a
     * @param string $b
     *
     * @return bool
     */
    public function greaterThan($a, $b)
    {
        return \gmp_cmp($a, $b) > 0;
    }

    /**
     * Converts arbitrary-length integer to PHP integer.
     *
     * @param string $a
     *
     * @return int
     */
    public function intval($a)
    {
        return \gmp_intval($a);
    }

    /**
     * Converts arbitrary-length integer to PHP string.
     *
     * @param string $a
     *
     * @return string
     */
    public function strval($a)
    {
        return \gmp_strval($a);
    }

    /**
     * Converts PHP integer to arbitrary-length integer.
     *
     * @param int $a
     *
     * @return string
     */
    public function get($a)
    {
        return \gmp_init($a);
    }
}
