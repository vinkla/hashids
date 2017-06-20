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
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * This is the runtime exception test class.
 *
 * @author Vincent Klaiber <hello@vinkla.com>
 */
class RuntimeExceptionTest extends TestCase
{
    public function testAdd()
    {
        $this->expectException(RuntimeException::class);

        Math::add(1, 2);
    }

    public function testMultiply()
    {
        $this->expectException(RuntimeException::class);

        Math::multiply(1, 2);
    }

    public function testDivide()
    {
        $this->expectException(RuntimeException::class);

        Math::divide(1, 2);
    }

    public function testGreaterThan()
    {
        $this->expectException(RuntimeException::class);

        Math::greaterThan('1', '2');
    }

    public function testMod()
    {
        $this->expectException(RuntimeException::class);

        Math::mod('1', '2');
    }
}
