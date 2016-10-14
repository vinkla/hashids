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

use Hashids\Hashids;

/**
 * This is the hashids test class.
 *
 * @author Ivan Akimov <ivan@barreleye.com>
 */
class HashidsTest extends AbstractTestCase
{
    private $hashids = null;
    private $minHashLength = 1000;
    private $customAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    private $maxId = 75; /* set higher to test locally */

    public function __construct()
    {
        $this->hashids = new Hashids('this is my salt');
        $this->hashidsMinLength = new Hashids('this is my salt', $this->minHashLength);
        $this->hashidsAlphabet = new Hashids('this is my salt', 0, $this->customAlphabet);
    }

    public function testCollisions()
    {
        foreach ([
            $this->hashids,
            $this->hashidsMinLength,
            $this->hashidsAlphabet,
        ] as $hashids) {
            $hashes = [];

            /* encode one number like [123] */

            for ($i = 0; $i != $this->maxId; ++$i) {
                $hashes[] = $hashids->encode($i);
            }

            $unique_array = array_unique($hashes);
            $this->assertSame(0, count($hashes) - count($unique_array));
        }
    }

    public function testMultiCollisions()
    {
        foreach ([
            $this->hashids,
            $this->hashidsMinLength,
            $this->hashidsAlphabet,
        ] as $hashids) {
            $hashes = [];
            $maxId = (int) ($this->maxId / 3);

            /* encode multiple numbers like [1, 2, 3] */

            for ($i = 0; $i != $maxId; ++$i) {
                for ($j = 0; $j != $maxId; ++$j) {
                    for ($k = 0; $k != $maxId; ++$k) {
                        $hashes[] = $hashids->encode($i, $j, $k);
                    }
                }
            }

            $unique_array = array_unique($hashes);
            $this->assertSame(0, count($hashes) - count($unique_array));
        }
    }

    public function testMinHashLength()
    {
        $hashes = [];

        for ($i = 0; $i != $this->maxId; ++$i) {
            $hash = $this->hashidsMinLength->encode($i);
            if (strlen($hash) < $this->minHashLength) {
                $hashes[] = $hash;
            }
        }

        $this->assertSame(0, count($hashes));
    }

    public function testRandomHashesDecoding()
    {
        $corrupt = $hashes = [];

        for ($i = 0; $i != $this->maxId; ++$i) {

            /* create a random hash */

            $randomHash = substr(md5(microtime()), rand(0, 10), rand(3, 12));
            if ($i % 2 == 0) {
                $randomHash = strtoupper($randomHash);
            }

            /* decode it; check that it's empty */

            $numbers = $this->hashids->decode($randomHash);
            if ($numbers) {

                /* could've accidentally generated correct hash, try to encode */

                $hash = call_user_func_array([$this->hashids, 'encode'], $numbers);
                if ($hash != $randomHash) {
                    $corrupt[] = $randomHash;
                }
            }
        }

        $this->assertSame(0, count($corrupt));
    }

    public function testCustomAlphabet()
    {
        $hashes = [];
        $alphabet_array = str_split($this->customAlphabet);

        for ($i = 0; $i != $this->maxId; ++$i) {
            $hash = $this->hashidsAlphabet->encode($i);
            $hash_array = str_split($hash);

            if (array_diff($hash_array, $alphabet_array)) {
                $hashes[] = $hash;
            }
        }

        $this->assertSame(0, count($hashes));
    }

    public function testBigValues()
    {
        $hashes = [];

        for ($i = PHP_INT_MAX, $j = $i - $this->maxId; $i != $j; --$i) {
            $hash = $this->hashids->encode($i);
            $numbers = $this->hashids->decode($hash);

            if (!$numbers || $i != $numbers[0]) {
                $hashes[] = $hash;
            }
        }

        $this->assertSame(0, count($hashes));
    }

    public function testOutOfBoundsValue()
    {
        $hash = $this->hashids->encode(PHP_INT_MAX + 1);
        $this->assertSame('', $hash);
    }

    public function testNegativeValue()
    {
        $hash = $this->hashids->encode(-1);
        $this->assertSame('', $hash);
    }

    public function testEncodingEmptyArray()
    {
        $hash = $this->hashids->encode([]);
        $this->assertSame('', $hash);
    }

    public function testEncodingWithoutParams()
    {
        $hash = $this->hashids->encode();
        $this->assertSame('', $hash);
    }

    public function testEncodingDecodingHex()
    {
        $testValue = '3ade68b1000fff';

        $id = $this->hashids->encodeHex($testValue);
        $this->assertTrue((bool) $id);

        $hex = $this->hashids->decodeHex($id);
        $this->assertSame($hex, $testValue);
    }
}
