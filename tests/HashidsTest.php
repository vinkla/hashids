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
use PHPUnit_Framework_TestCase;

/**
 * This is the hashids test class.
 *
 * @author Ivan Akimov <ivan@barreleye.com>
 */
class HashidsTest extends PHPUnit_Framework_TestCase
{
    private $hashids = null;
    private $salt = 'this is my salt';
    private $min_hash_length = 1000;
    private $custom_alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    private $max_id = 75; /* set higher to test locally */

    public function __construct()
    {
        $this->hashids = new Hashids($this->salt);
        $this->hashids_min_length = new Hashids($this->salt, $this->min_hash_length);
        $this->hashids_alphabet = new Hashids($this->salt, 0, $this->custom_alphabet);
    }

    public function testCollisions()
    {
        foreach ([
            $this->hashids,
            $this->hashids_min_length,
            $this->hashids_alphabet,
        ] as $hashids) {
            $hashes = [];

            /* encode one number like [123] */

            for ($i = 0; $i != $this->max_id; ++$i) {
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
            $this->hashids_min_length,
            $this->hashids_alphabet,
        ] as $hashids) {
            $hashes = [];
            $max_id = (int) ($this->max_id / 3);

            /* encode multiple numbers like [1, 2, 3] */

            for ($i = 0; $i != $max_id; ++$i) {
                for ($j = 0; $j != $max_id; ++$j) {
                    for ($k = 0; $k != $max_id; ++$k) {
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

        for ($i = 0; $i != $this->max_id; ++$i) {
            $hash = $this->hashids_min_length->encode($i);
            if (strlen($hash) < $this->min_hash_length) {
                $hashes[] = $hash;
            }
        }

        $this->assertSame(0, count($hashes));
    }

    public function testRandomHashesDecoding()
    {
        $corrupt = $hashes = [];

        for ($i = 0; $i != $this->max_id; ++$i) {

            /* create a random hash */

            $random_hash = substr(md5(microtime()), rand(0, 10), rand(3, 12));
            if ($i % 2 == 0) {
                $random_hash = strtoupper($random_hash);
            }

            /* decode it; check that it's empty */

            $numbers = $this->hashids->decode($random_hash);
            if ($numbers) {

                /* could've accidentally generated correct hash, try to encode */

                $hash = call_user_func_array([$this->hashids, 'encode'], $numbers);
                if ($hash != $random_hash) {
                    $corrupt[] = $random_hash;
                }
            }
        }

        $this->assertSame(0, count($corrupt));
    }

    public function testCustomAlphabet()
    {
        $hashes = [];
        $alphabet_array = str_split($this->custom_alphabet);

        for ($i = 0; $i != $this->max_id; ++$i) {
            $hash = $this->hashids_alphabet->encode($i);
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
        $max_int_value = $this->hashids->getMaxIntValue();

        for ($i = $this->hashids->getMaxIntValue(), $j = $i - $this->max_id; $i != $j; --$i) {
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
        $hash = $this->hashids->encode($this->hashids->getMaxIntValue() + 1);
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
