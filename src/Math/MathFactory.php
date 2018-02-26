<?php

/*
 * This file is part of Hashids.
 *
 * (c) Ivan Akimov <ivan@barreleye.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hashids\Math;

use RuntimeException;

/**
 * Factory for dynamically creating a MathInterface instance based on
 * available extensions.
 *
 * @author Johnson Page <wjpage@gmail.com>
 */
class MathFactory
{
    /**
     * Create a new MathInterface instance.
     *
     * @throws RuntimeException
     *
     * @return MathInterface
     *
     * @codeCoverageIgnore
     */
    public static function create()
    {
        if (extension_loaded('gmp')) {
            return new Gmp();
        } elseif (extension_loaded('bcmath')) {
            return new Bc();
        } else {
            throw new RuntimeException('Missing BC Math or GMP extension.');
        }
    }
}
