<?php

/*
 * This file is part of Hashids.
 *
 * (c) Ivan Akimov <ivan@barreleye.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hashids\Support;

/**
 * This is the str class.
 *
 * @author Vincent Klaiber <hello@vinkla.com>
 */
class Str
{
    /**
     * Get string value of a variable.
     *
     * @param mixed $var
     *
     * @return string
     */
    public static function value($var)
    {
        if (function_exists('gmp_strval')) {
            return gmp_strval($var);
        }

        return strval($var);
    }
}
