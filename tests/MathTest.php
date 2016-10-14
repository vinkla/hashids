<?php

/*
 * This file is part of Hashids.
 *
 * (c) Ivan Akimov <ivan@barreleye.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hashids\Tests;

use Hashids\Math;

/**
 * This is the math test class.
 *
 * @author Vincent Klaiber <hello@vinkla.com>
 */
class MathTest extends AbstractTestCase
{
    public function testAdd()
    {
        // TODO: This should test big numbers.
        $this->assertSame(3, Math::add(1, 2));
    }

    public function testDivide()
    {
        // TODO: This should test big numbers.
        $this->assertSame(2, Math::divide(4, 2));
    }

    public function testPow()
    {
        // TODO: This should test big numbers.
        $this->assertSame(16, Math::pow(4, 2));
    }
}
