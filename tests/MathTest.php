<?php

/**
 * Copyright (c) Ivan Akimov.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/vinkla/hashids
 */

namespace Hashids\Tests;

use Hashids\Math\BCMath;
use Hashids\Math\Gmp;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class MathTest extends TestCase
{
    public function mathProvider()
    {
        if (extension_loaded('gmp')) {
            return [
                [new Gmp()]
            ];
        }

        if (extension_loaded('bcmath')) {
            return [
                [new BCMath()]
            ];
        }

        throw new RuntimeException('Missing BC Math or GMP extension.');
    }

    /** @dataProvider mathProvider */
    public function testAdd($math)
    {
        $this->assertEquals($math->get(3), $math->add(1, 2));
    }

    /** @dataProvider mathProvider */
    public function testMultiply($math)
    {
        $this->assertEquals($math->get(12), $math->multiply(2, 6));
    }

    /** @dataProvider mathProvider */
    public function testDivide($math)
    {
        $this->assertEquals($math->get(2), $math->divide(4, 2));
    }

    /** @dataProvider mathProvider */
    public function testGreaterThan($math)
    {
        $this->assertTrue($math->greaterThan('18446744073709551615', '9223372036854775807'));
        $this->assertFalse($math->greaterThan('9223372036854775807', '18446744073709551615'));
        $this->assertFalse($math->greaterThan('9223372036854775807', '9223372036854775807'));
    }

    /** @dataProvider mathProvider */
    public function testMod($math)
    {
        $this->assertEquals($math->get(15), $math->mod('18446744073709551615', '100'));
    }

    /** @dataProvider mathProvider */
    public function testIntval($math)
    {
        $this->assertSame(9223372036854775807, $math->intval('9223372036854775807'));
    }

    /** @dataProvider mathProvider */
    public function testStrval($math)
    {
        $this->assertSame('18446744073709551615', $math->strval($math->add('0', '18446744073709551615')));
    }
}
