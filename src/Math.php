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
     * @throws \RuntimeException
     */
    public static function add($a, $b)
    {
        return MathFactory::create()->add($a, $b);
    }

    /**
     * @throws \RuntimeException
     */
    public static function multiply($a, $b)
    {
        return MathFactory::create()->multiply($a, $b);
    }

    /**
     * @throws \RuntimeException
     */
    public static function divide($a, $b)
    {
        return MathFactory::create()->divide($a, $b);
    }

    /**
     * @throws \RuntimeException
     */
    public static function mod($n, $d)
    {
        return MathFactory::create()->mod($n, $d);
    }

    /**
     * @throws \RuntimeException
     */
    public static function greaterThan($a, $b)
    {
        return MathFactory::create()->greaterThan($a, $b);
    }

    public static function intval($a)
    {
        return MathFactory::create()->intval($a);
    }

    public static function strval($a)
    {
        return MathFactory::create()->strval($a);
    }

    public static function get($a)
    {
        return MathFactory::create()->get($a);
    }
}
