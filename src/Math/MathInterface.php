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

interface MathInterface
{
    /**
     * Add two arbitrary-length integers.
     *
     * @param string $a
     * @param string $b
     *
     * @return string
     */
    public function add($a, $b);

    /**
     * Multiply two arbitrary-length integers.
     *
     * @param string $a
     * @param string $b
     *
     * @return string
     */
    public function multiply($a, $b);

    /**
     * Divide two arbitrary-length integers.
     *
     * @param string $a
     * @param string $b
     *
     * @return string
     */
    public function divide($a, $b);

    /**
     * Compute arbitrary-length integer modulo.
     *
     * @param string $n
     * @param string $d
     *
     * @return string
     */
    public function mod($n, $d);

    /**
     * Compares two arbitrary-length integers.
     *
     * @param string $a
     * @param string $b
     *
     * @return bool
     */
    public function greaterThan($a, $b);

    /**
     * Converts arbitrary-length integer to PHP integer.
     *
     * @param string $a
     *
     * @return int
     */
    public function intval($a);

    /**
     * Converts arbitrary-length integer to PHP string.
     *
     * @param string $a
     *
     * @return string
     */
    public function strval($a);

    /**
     * Converts PHP integer to arbitrary-length integer.
     *
     * @param int $a
     *
     * @return string
     */
    public function get($a);
}
