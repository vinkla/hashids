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

use Hashids\Math\Bc;
use Hashids\Math\Gmp;
use PHPUnit\Framework\TestCase;

/**
 * Test available MathInterface classes.
 *
 * @author Vincent Klaiber <hello@vinkla.com>
 * @author Johnson Page <jwpage@gmail.com>
 */
class MathInterfaceTest extends TestCase
{
    public function mathProvider()
    {
        return [
            [new Bc()],
            [new Gmp()],
        ];
    }

    /**
     * @dataProvider mathProvider
     */
    public function testAdd($math)
    {
        $this->assertEquals($math->get(3), $math->add(1, 2));
    }

    /**
     * @dataProvider mathProvider
     */
    public function testMultiply($math)
    {
        $this->assertEquals($math->get(12), $math->multiply(2, 6));
    }

    /**
     * @dataProvider mathProvider
     */
    public function testDivide($math)
    {
        $this->assertEquals($math->get(2), $math->divide(4, 2));
    }

    /**
     * @dataProvider mathProvider
     */
    public function testGreaterThan($math)
    {
        $this->assertTrue($math->greaterThan('18446744073709551615', '9223372036854775807'));
        $this->assertFalse($math->greaterThan('9223372036854775807', '18446744073709551615'));
        $this->assertFalse($math->greaterThan('9223372036854775807', '9223372036854775807'));
    }

    /**
     * @dataProvider mathProvider
     */
    public function testMod($math)
    {
        $this->assertEquals($math->get(15), $math->mod('18446744073709551615', '100'));
    }

    /**
     * @dataProvider mathProvider
     */
    public function testIntval($math)
    {
        $this->assertSame(9223372036854775807, $math->intval('9223372036854775807'));
    }

    /**
     * @dataProvider mathProvider
     */
    public function testStrval($math)
    {
        $this->assertSame('18446744073709551615', $math->strval($math->add('0', '18446744073709551615')));
    }
}
