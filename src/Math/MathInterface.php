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
     */
    public function add(string $a, string $b): string;

    /**
     * Multiply two arbitrary-length integers.
     */
    public function multiply(string $a, string $b): string;

    /**
     * Divide two arbitrary-length integers.
     */
    public function divide(string $a, string $b): string;

    /**
     * Compute arbitrary-length integer modulo.
     */
    public function mod(string $n, string $d): string;

    /**
     * Compares two arbitrary-length integers.
     */
    public function greaterThan(string $a, string $b): bool;

    /**
     * Converts arbitrary-length integer to PHP integer.
     */
    public function intval(string $a): int;

    /**
     * Converts arbitrary-length integer to PHP string.
     */
    public function strval(string $a): string;

    /**
     * Converts PHP integer to arbitrary-length integer.
     */
    public function get(int $a): string;
}
