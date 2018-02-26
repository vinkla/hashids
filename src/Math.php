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

use Hashids\Math\MathFactory;

/**
 * This is the math class.
 *
 * @author Vincent Klaiber <hello@vinkla.com>
 * @author Jakub Kramarz <lenwe@lenwe.net>
 *
 * @deprecated No longer used in internal code.
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
    public function add($a, $b)
    {
        return MathFactory::create()->add($a, $b);
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
    public function multiply($a, $b)
    {
        return MathFactory::create()->multiply($a, $b);
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
    public function divide($a, $b)
    {
        return MathFactory::create()->divide($a, $b);
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
    public function mod($n, $d)
    {
        return MathFactory::create()->mod($n, $d);
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
    public function greaterThan($a, $b)
    {
        return MathFactory::create()->greaterThan($a, $b);
    }

    /**
     * Converts arbitrary-length integer to PHP integer.
     *
     * @param string $a
     *
     * @throws \RuntimeException
     *
     * @return int
     */
    public function intval($a)
    {
        return MathFactory::create()->intval($a);
    }

    /**
     * Converts arbitrary-length integer to PHP string.
     *
     * @param string $a
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function strval($a)
    {
        return MathFactory::create()->strval($a);
    }

    /**
     * Converts PHP integer to arbitrary-length integer.
     *
     * @param int $a
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function get($a)
    {
        return MathFactory::create()->get($a);
    }
}
