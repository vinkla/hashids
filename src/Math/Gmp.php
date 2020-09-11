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

use function gmp_add;
use function gmp_cmp;
use function gmp_div_q;
use function gmp_init;
use function gmp_intval;
use function gmp_mod;
use function gmp_mul;
use function gmp_strval;

class Gmp implements MathInterface
{
    /**
     * {@inheritDoc}
     */
    public function add(string $a, string $b): string
    {
        return gmp_add($a, $b);
    }

    /**
     * {@inheritDoc}
     */
    public function multiply(string $a, string $b): string
    {
        return gmp_mul($a, $b);
    }

    /**
     * {@inheritDoc}
     */
    public function divide(string $a, string $b): string
    {
        return gmp_div_q($a, $b);
    }

    /**
     * {@inheritDoc}
     */
    public function mod(string $n, string $d): string
    {
        return gmp_mod($n, $d);
    }

    /**
     * {@inheritDoc}
     */
    public function greaterThan(string $a, string $b): bool
    {
        return gmp_cmp($a, $b) > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function intval(string $a): int
    {
        return gmp_intval($a);
    }

    /**
     * {@inheritDoc}
     */
    public function strval(string $a): string
    {
        return gmp_strval($a);
    }

    /**
     * {@inheritDoc}
     */
    public function get(int $a): string
    {
        return gmp_init($a);
    }
}
