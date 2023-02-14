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

use Hashids\Hashids;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class HashidsTest extends TestCase
{
    public function testSmallAlphabet()
    {
        $this->expectException(InvalidArgumentException::class);

        new Hashids('', 0, '1234567890');
    }

    public function testAlphabetWithSpace()
    {
        $this->expectException(InvalidArgumentException::class);

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

    public static function alphabetProvider()
    {
        return [
            ['cCsSfFhHuUiItT01'],
            ['abdegjklCFHISTUc'],
            ['abdegjklmnopqrSF'],
            ['abdegjklmnopqrvwxyzABDEGJKLMNOPQRVWXYZ1234567890'],
            ['abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890`~!@#$%^&*()-_=+\\|\'";:/?.>,<{[}]'],
            ['`~!@#$%^&*()-_=+\\|\'";:/?.>,<{[}]'],
            ['áàãăâeéèêiíìĩoóòõôơuúùũưyýỳđ'],
        ];
    }

    /** @dataProvider alphabetProvider */
    public function testAlphabet($alphabets)
    {
        $numbers = [1, 2, 3];

        $hashids = new Hashids('', 0, $alphabets);

        $id = $hashids->encode($numbers);
        $this->assertSame($hashids->decode($id), $numbers);
    }

    public static function saltProvider()
    {
        return [
            [''],
            ['0'],
            ['   '],
            ['this is my salt'],
            ['abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890`~!@#$%^&*()-_=+\\|\'";:/?.>,<{[}]'],
            ['`~!@#$%^&*()-_=+\\|\'";:/?.>,<{[}]'],
            ['!áàãăâ eéèê iíìĩ oóòõôơ uúùũư yýỳ đ'],
        ];
    }

    /** @dataProvider saltProvider */
    public function testSalt($salts)
    {
        $numbers = [1, 2, 3];

        $hashids = new Hashids($salts);

        $id = $hashids->encode($numbers);

        $this->assertSame($hashids->decode($id), $numbers);
    }

    public static function minLengthProvider()
    {
        return [
            [0],
            [1],
            [10],
            [999],
            [1000],
        ];
    }

    /** @dataProvider minLengthProvider */
    public function testMinLength($lengths)
    {
        $numbers = [1, 2, 3];

        $hashids = new Hashids('', $lengths);

        $id = $hashids->encode($numbers);

        $this->assertSame($hashids->decode($id), $numbers);
        $this->assertLessThanOrEqual(strlen($id), $lengths);
    }

    public static function encodeTypesProvider()
    {
        return [
            [[1, 2, 3]],
            [['1', '2', '3']],
            [['1', 2, '3']],
        ];
    }

    /** @dataProvider encodeTypesProvider */
    public function testEncodeTypes($params)
    {
        $numbers = [1, 2, 3];

        $hashids = new Hashids();

        $id = $hashids->encode($numbers);
        $decodedNumbers = $hashids->decode($id);
        $this->assertSame($id, $hashids->encode($decodedNumbers));

        $id = call_user_func_array([$hashids, 'encode'], $params);
        $decodedNumbers = $hashids->decode($id);
        $this->assertSame($id, $hashids->encode($decodedNumbers));
    }

    public static function defaultParamsProvider()
    {
        return [
            ['gY', [0]],
            ['jR', [1]],
            ['R8ZN0', [928728]],
            ['o2fXhV', [1, 2, 3]],
            ['jRfMcP', [1, 0, 0]],
            ['jQcMcW', [0, 0, 1]],
            ['gYcxcr', [0, 0, 0]],
            ['gLpmopgO6', [1000000000000]],
            ['lEW77X7g527', [9007199254740991]],
            ['BrtltWt2tyt1tvt7tJt2t1tD', [5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5]],
            ['G6XOnGQgIpcVcXcqZ4B8Q8B9y', [10000000000, 0, 0, 0, 999999999999999]],
            ['5KoLLVL49RLhYkppOplM6piwWNNANny8N', [9007199254740991, 9007199254740991, 9007199254740991]],
            ['BPg3Qx5f8VrvQkS16wpmwIgj9Q4Jsr93gqx', [1000000001, 1000000002, 1000000003, 1000000004, 1000000005]],
            ['1wfphpilsMtNumCRFRHXIDSqT2UPcWf1hZi3s7tN', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20]],
        ];
    }

    /** @dataProvider defaultParamsProvider */
    public function testDefaultParams($id, $numbers)
    {
        $hashids = new Hashids();

        $encodedId = $hashids->encode($numbers);
        $decodedNumbers = $hashids->decode($encodedId);

        $this->assertSame($id, $encodedId);
        $this->assertSame($id, call_user_func_array([$hashids, 'encode'], $numbers));
        $this->assertSame($numbers, $decodedNumbers);
    }

    public static function customParamsProvider()
    {
        return [
            ['nej1m3d5a6yn875e7gr9kbwpqol02q', [0]],
            ['dw1nqdp92yrajvl9v6k3gl5mb0o8ea', [1]],
            ['onqr0bk58p642wldq14djmw21ygl39', [928728]],
            ['18apy3wlqkjvd5h1id7mn5ore2d06b', [1, 2, 3]],
            ['o60edky1ng3vl9hbfavwr5pa2q8mb9', [1, 0, 0]],
            ['o60edky1ng3vlqfbfp4wr5pa2q8mb9', [0, 0, 1]],
            ['qek2a08gpl575efrfd7yomj9dwbr63', [0, 0, 0]],
            ['m3d5a6yn875rae8y81a94gr9kbwpqo', [1000000000000]],
            ['1q3y98ln48w96kpo0wgk314w5mak2d', [9007199254740991]],
            ['op7qrcdc3cgc2c0cbcrcoc5clce4d6', [5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5]],
            ['5430bd2jo0lxyfkfjfyojej5adqdy4', [10000000000, 0, 0, 0, 999999999999999]],
            ['aa5kow86ano1pt3e1aqm239awkt9pk380w9l3q6', [9007199254740991, 9007199254740991, 9007199254740991]],
            ['mmmykr5nuaabgwnohmml6dakt00jmo3ainnpy2mk', [1000000001, 1000000002, 1000000003, 1000000004, 1000000005]],
            ['w1hwinuwt1cbs6xwzafmhdinuotpcosrxaz0fahl', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20]],
        ];
    }

    /** @dataProvider customParamsProvider */
    public function testCustomParams($id, $numbers)
    {
        $minLength = 30;

        $hashids = new Hashids('this is my salt', $minLength, 'xzal86grmb4jhysfoqp3we7291kuct5iv0nd');

        $encodedId = $hashids->encode($numbers);
        $decodedNumbers = $hashids->decode($encodedId);

        $this->assertSame($id, $encodedId);
        $this->assertSame($id, call_user_func_array([$hashids, 'encode'], $numbers));
        $this->assertSame($numbers, $decodedNumbers);
        $this->assertLessThanOrEqual(strlen($encodedId), $minLength);
    }

    public static function defaultParamsHexProvider()
    {
        return [
            ['wpVL4j9g', 'deadbeef'],
            ['kmP69lB3xv', 'abcdef123456'],
            ['47JWg0kv4VU0G2KBO2', 'ABCDDD6666DDEEEEEEEEE'],
            ['y42LW46J9luq3Xq9XMly', '507f1f77bcf86cd799439011'],
            ['m1rO8xBQNquXmLvmO65BUO9KQmj', 'f00000fddddddeeeee4444444ababab'],
            ['wBlnMA23NLIQDgw7XxErc2mlNyAjpw', 'abcdef123456abcdef123456abcdef123456'],
            ['VwLAoD9BqlT7xn4ZnBXJFmGZ51ZqrBhqrymEyvYLIP199', 'f000000000000000000000000000000000000000000000000000f'],
            ['nBrz1rYyV0C0XKNXxB54fWN0yNvVjlip7127Jo3ri0Pqw', 'fffffffffffffffffffffffffffffffffffffffffffffffffffff'],
        ];
    }

    /** @dataProvider defaultParamsHexProvider */
    public function testDefaultParamsHex($id, $hex)
    {
        $hashids = new Hashids();

        $encodedId = $hashids->encodeHex($hex);
        $decodedHex = $hashids->decodeHex($encodedId);

        $this->assertSame($id, $encodedId);
        $this->assertSame(strtolower($hex), $decodedHex);
    }

    public static function customParamsHexProvider()
    {
        return [
            ['0dbq3jwa8p4b3gk6gb8bv21goerm96', 'deadbeef'],
            ['190obdnk4j02pajjdande7aqj628mr', 'abcdef123456'],
            ['a1nvl5d9m3yo8pj1fqag8p9pqw4dyl', 'ABCDDD6666DDEEEEEEEEE'],
            ['1nvlml93k3066oas3l9lr1wn1k67dy', '507f1f77bcf86cd799439011'],
            ['mgyband33ye3c6jj16yq1jayh6krqjbo', 'f00000fddddddeeeee4444444ababab'],
            ['9mnwgllqg1q2tdo63yya35a9ukgl6bbn6qn8', 'abcdef123456abcdef123456abcdef123456'],
            ['edjrkn9m6o69s0ewnq5lqanqsmk6loayorlohwd963r53e63xmml29', 'f000000000000000000000000000000000000000000000000000f'],
            ['grekpy53r2pjxwyjkl9aw0k3t5la1b8d5r1ex9bgeqmy93eata0eq0', 'fffffffffffffffffffffffffffffffffffffffffffffffffffff'],
        ];
    }

    /** @dataProvider customParamsHexProvider */
    public function testCustomParamsHex($id, $hex)
    {
        $minLength = 30;

        $hashids = new Hashids('this is my salt', $minLength, 'xzal86grmb4jhysfoqp3we7291kuct5iv0nd');

        $encodedId = $hashids->encodeHex($hex);
        $decodedHex = $hashids->decodeHex($encodedId);

        $this->assertSame($id, $encodedId);
        $this->assertSame(strtolower($hex), $decodedHex);
        $this->assertLessThanOrEqual(strlen($encodedId), $minLength);
    }

    public static function bigNumberDataProvider()
    {
        return [
            [2147483647, 'ykJWW1g'], //max 32-bit signed integer
            [4294967295, 'j4r6j8Y'], // max 32-bit unsigned integer
            ['9223372036854775807', 'jvNx4BjM5KYjv'], // max 64-bit signed integer
            ['18446744073709551615', 'zXVjmzBamYlqX'], // max 64-bit unsigned integer
        ];
    }

    /** @dataProvider bigNumberDataProvider */
    public function testBigNumberEncode($number, $hash)
    {
        $hashids = new Hashids('this is my salt');
        $encoded = $hashids->encode($number);
        $this->assertEquals($hash, $encoded);
    }

    /** @dataProvider bigNumberDataProvider */
    public function testBigNumberDecode($number, $hash)
    {
        $hashids = new Hashids('this is my salt');
        $decoded = $hashids->decode($hash);
        $this->assertEquals($number, $decoded[0]);
    }

    public static function jsHashidsDataProvider()
    {
        return [
            ['', 0, 'áàãăâeéèêiíìĩoóòõôơuúùũưyýỳđ', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10], 'íóuđáìàúãỳăyâôeyiôuĩ'],
            ['世界', 0, 'áàãăâeéèêiíìĩoóòõôơuúùũưyýỳđ', [9007199254740991], 'óôòúỳưúoỳééưýy'],
            ['', 0, 'cCsSfFhHuUiItT01', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10], '10h10i00s100t010u110C000F1000H0110I1010'],
            ['', 9, '零0一1二2三3四4五5六6七7七8八9九', [4231], '5三77九58三九'],
        ];
    }

    /** @dataProvider jsHashidsDataProvider */
    public function testJsHashidsCompatible($salt, $minHashLength, $alphabet, $numbers, $hash)
    {
        $hashids = new Hashids($salt, $minHashLength, $alphabet);
        $this->assertEquals($hash, $hashids->encode($numbers));
    }

    /**
     * @requires function bcscale
     */
    public function testBehaviourForDifferentBCMathAccuracy()
    {
        bcscale(2);
        $hashids = new Hashids('this is my salt', 12);
        $encoded = $hashids->encode(1);
        $this->assertEquals('DngB0NV05ev1', $encoded);
    }
}
