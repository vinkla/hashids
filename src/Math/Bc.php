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

use function bcadd;
use function bccomp;
use function bcdiv;
use function bcmod;
use function bcmul;

class Bc implements MathInterface
{
    /**
     * {@inheritDoc}
     */
    public function add(string $a, string $b): string
    {
        return bcadd($a, $b, 0);
    }

    /**
     * {@inheritDoc}
     */
    public function multiply(string $a, string $b): string
    {
        return bcmul($a, $b, 0);
    }

    /**
     * {@inheritDoc}
     */
    public function divide(string $a, string $b): string
    {
        return bcdiv($a, $b, 0);
    }

    /**
     * {@inheritDoc}
     */
    public function mod(string $n, string $d): string
    {
        return bcmod($n, $d);
    }

    /**
     * {@inheritDoc}
     */
    public function greaterThan(string $a, string $b): bool
    {
        return bccomp($a, $b, 0) > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function intval(string $a): int
    {
        return (int) $a;
    }

    /**
     * {@inheritDoc}
     */
    public function strval(string $a): string
    {
        return $a;
    }

    /**
     * {@inheritDoc}
     */
    public function get(int $a): string
    {
        return $a;
    }
}
