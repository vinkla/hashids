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
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSmallAlphabet()
    {
        new Hashids('', 0, '1234567890');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAlphabetWithSpace()
    {
        new Hashids('', 0, 'a cdefghijklmnopqrstuvwxyz');
    }

    public function testBadInput()
    {
        $hashids = new Hashids();

        $this->assertSame('', $hashids->encode());
        $this->assertSame('', $hashids->encode([]));
        $this->assertSame('', $hashids->encode(-1));
        $this->assertSame('', $hashids->encode('6B'));
        $this->assertSame('', $hashids->encode('123a'));
        $this->assertSame('', $hashids->encode(null));
        $this->assertSame('', $hashids->encode(['z']));

        $this->assertSame([], $hashids->decode(''));
        $this->assertSame([], $hashids->decode('f'));

        $this->assertSame('', $hashids->encodeHex('z'));

        $this->assertSame('', $hashids->decodeHex('f'));
    }

    public function testAlphabet()
    {
        $numbers = [1, 2, 3];

        $alphabets = [
            'cCsSfFhHuUiItT01',
            'abdegjklCFHISTUc',
            'abdegjklmnopqrSF',
            'abdegjklmnopqrvwxyzABDEGJKLMNOPQRVWXYZ1234567890',
            'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890`~!@#$%^&*()-_=+\\|\'";:/?.>,<{[}]',
            '`~!@#$%^&*()-_=+\\|\'";:/?.>,<{[}]',
        ];

        foreach ($alphabets as $alphabet) {
            $hashids = new Hashids('', 0, $alphabet);

            $id = $hashids->encode($numbers);

            $this->assertSame($hashids->decode($id), $numbers);
        }
    }

    public function testParams()
    {
        $maps = [
            'nej1m3d5a6yn875e7gr9kbwpqol02q' => [0],
            'dw1nqdp92yrajvl9v6k3gl5mb0o8ea' => [1],
            'onqr0bk58p642wldq14djmw21ygl39' => [928728],
            '18apy3wlqkjvd5h1id7mn5ore2d06b' => [1, 2, 3],
            'o60edky1ng3vl9hbfavwr5pa2q8mb9' => [1, 0, 0],
            'o60edky1ng3vlqfbfp4wr5pa2q8mb9' => [0, 0, 1],
            'qek2a08gpl575efrfd7yomj9dwbr63' => [0, 0, 0],
            'm3d5a6yn875rae8y81a94gr9kbwpqo' => [1000000000000],
            '1q3y98ln48w96kpo0wgk314w5mak2d' => [9007199254740991],
            'op7qrcdc3cgc2c0cbcrcoc5clce4d6' => [5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5],
            '5430bd2jo0lxyfkfjfyojej5adqdy4' => [10000000000, 0, 0, 0, 999999999999999],
            'aa5kow86ano1pt3e1aqm239awkt9pk380w9l3q6' => [9007199254740991, 9007199254740991, 9007199254740991],
            'mmmykr5nuaabgwnohmml6dakt00jmo3ainnpy2mk' => [1000000001, 1000000002, 1000000003, 1000000004, 1000000005],
            'w1hwinuwt1cbs6xwzafmhdinuotpcosrxaz0fahl' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
        ];

        $minLength = 30;

        $hashids = new Hashids('this is my salt', $minLength, 'xzal86grmb4jhysfoqp3we7291kuct5iv0nd');

        foreach ($maps as $id => $numbers) {
            $encodedId = $hashids->encode($numbers);
            $decodedNumbers = $hashids->encode($encodedId);

            $this->assertSame($id, $encodedId);
            $this->assertSame($numbers, $decodedNumbers);
            $this->assertLessThanOrEqual(strlen($encodedId), $minLength);
        }
    }

    public function testParamsHex()
    {
        $maps = [
            '0dbq3jwa8p4b3gk6gb8bv21goerm96' => 'deadbeef',
            '190obdnk4j02pajjdande7aqj628mr' => 'abcdef123456',
            'a1nvl5d9m3yo8pj1fqag8p9pqw4dyl' => 'ABCDDD6666DDEEEEEEEEE',
            '1nvlml93k3066oas3l9lr1wn1k67dy' => '507f1f77bcf86cd799439011',
            'mgyband33ye3c6jj16yq1jayh6krqjbo' => 'f00000fddddddeeeee4444444ababab',
            '9mnwgllqg1q2tdo63yya35a9ukgl6bbn6qn8' => 'abcdef123456abcdef123456abcdef123456',
            'edjrkn9m6o69s0ewnq5lqanqsmk6loayorlohwd963r53e63xmml29' => 'f000000000000000000000000000000000000000000000000000f',
            'grekpy53r2pjxwyjkl9aw0k3t5la1b8d5r1ex9bgeqmy93eata0eq0' => 'fffffffffffffffffffffffffffffffffffffffffffffffffffff',
        ];

        $minLength = 30;

        $hashids = new Hashids('this is my salt', $minLength, 'xzal86grmb4jhysfoqp3we7291kuct5iv0nd');

        foreach ($maps as $id => $hex) {
            $encodedId = $hashids->encodeHex($hex);
            $decodedHex = $hashids->decodeHex($encodedId);

            $this->assertSame($id, $encodedId);
            $this->assertSame(strtolower($hex), $decodedHex);
            $this->assertLessThanOrEqual(strlen($encodedId), $minLength);
        }
    }

    public function testSalt()
    {
        $numbers = [1, 2, 3];

        $salts = [
            '',
            '   ',
            'this is my salt',
            'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890`~!@#$%^&*()-_=+\\|\'";:/?.>,<{[}]',
            '`~!@#$%^&*()-_=+\\|\'";:/?.>,<{[}]',
        ];

        foreach ($salts as $salt) {
            $hashids = new Hashids($salt);

            $id = $hashids->encode($numbers);

            $this->assertSame($hashids->decode($id), $numbers);
        }
    }

    public function testMinLength()
    {
        $numbers = [1, 2, 3];

        foreach ([0, 1, 10, 999, 1000] as $length) {
            $hashids = new Hashids('', $length);

            $id = $hashids->encode($numbers);

            $this->assertSame($hashids->decode($id), $numbers);
            $this->assertLessThanOrEqual(strlen($id), $length);
        }
    }
}
