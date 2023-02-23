<?php

/**
 * Copyright (c) Ivan Akimov.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/vinkla/hashids
 */

namespace Hashids;

use Hashids\Math\BCMath;
use Hashids\Math\Gmp;
use Hashids\Math\MathInterface;
use InvalidArgumentException;
use RuntimeException;

class Hashids implements HashidsInterface
{
    public const GUARD_DIV = 12;
    public const SEP_DIV = 3.5;
    protected MathInterface $math;
    protected array $shuffledAlphabets;
    protected int $minHashLength;
    protected string $alphabet;
    protected string $guards;
    protected string $salt;
    protected string $seps = 'cfhistuCFHISTU';

    /** @throws \InvalidArgumentException */
    public function __construct(
        string $salt = '',
        int $minHashLength = 0,
        string $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
    ) {
        $this->salt = mb_convert_encoding($salt, 'UTF-8', mb_detect_encoding($salt));
        $this->minHashLength = $minHashLength;
        $alphabet = mb_convert_encoding($alphabet, 'UTF-8', mb_detect_encoding($alphabet));
        $this->alphabet = implode('', array_unique($this->multiByteSplit($alphabet)));
        $this->math = $this->getMathExtension();

        if (mb_strlen($this->alphabet) < 16) {
            throw new InvalidArgumentException('The Hashids alphabet must contain at least 16 unique characters.');
        }

        if (false !== mb_strpos($this->alphabet, ' ')) {
            throw new InvalidArgumentException('The Hashids alphabet can\'t contain spaces.');
        }

        $alphabetArray = $this->multiByteSplit($this->alphabet);
        $sepsArray = $this->multiByteSplit($this->seps);
        $this->seps = implode('', array_intersect($sepsArray, $alphabetArray));
        $this->alphabet = implode('', array_diff($alphabetArray, $sepsArray));
        $this->seps = $this->shuffle($this->seps, $this->salt);

        if (!$this->seps || (mb_strlen($this->alphabet) / mb_strlen($this->seps)) > self::SEP_DIV) {
            $sepsLength = (int) ceil(mb_strlen($this->alphabet) / self::SEP_DIV);

            if ($sepsLength > mb_strlen($this->seps)) {
                $diff = $sepsLength - mb_strlen($this->seps);
                $this->seps .= mb_substr($this->alphabet, 0, $diff);
                $this->alphabet = mb_substr($this->alphabet, $diff);
            }
        }

        $this->alphabet = $this->shuffle($this->alphabet, $this->salt);
        $guardCount = (int) ceil(mb_strlen($this->alphabet) / self::GUARD_DIV);

        if (mb_strlen($this->alphabet) < 3) {
            $this->guards = mb_substr($this->seps, 0, $guardCount);
            $this->seps = mb_substr($this->seps, $guardCount);
        } else {
            $this->guards = mb_substr($this->alphabet, 0, $guardCount);
            $this->alphabet = mb_substr($this->alphabet, $guardCount);
        }
    }

    public function encode(...$numbers): string
    {
        $ret = '';

        if (1 === count($numbers) && is_array($numbers[0])) {
            $numbers = $numbers[0];
        }

        if (!$numbers) {
            return $ret;
        }

        foreach ($numbers as $number) {
            $isNumber = ctype_digit((string) $number);

            if (!$isNumber) {
                return $ret;
            }
        }

        $alphabet = $this->alphabet;
        $numbersSize = count($numbers);
        $numbersHashInt = 0;

        foreach ($numbers as $i => $number) {
            $numbersHashInt += $this->math->intval($this->math->mod($number, $i + 100));
        }

        $lottery = $ret = mb_substr($alphabet, $numbersHashInt % mb_strlen($alphabet), 1);
        foreach ($numbers as $i => $number) {
            $alphabet = $this->shuffle($alphabet, mb_substr($lottery . $this->salt . $alphabet, 0, mb_strlen($alphabet)));
            $ret .= $last = $this->hash($number, $alphabet);

            if ($i + 1 < $numbersSize) {
                $number %= (mb_ord($last, 'UTF-8') + $i);
                $sepsIndex = $this->math->intval($this->math->mod($number, mb_strlen($this->seps)));
                $ret .= mb_substr($this->seps, $sepsIndex, 1);
            }
        }

        if (mb_strlen($ret) < $this->minHashLength) {
            $guardIndex = ($numbersHashInt + mb_ord(mb_substr($ret, 0, 1), 'UTF-8')) % mb_strlen($this->guards);

            $guard = mb_substr($this->guards, $guardIndex, 1);
            $ret = $guard . $ret;

            if (mb_strlen($ret) < $this->minHashLength) {
                $guardIndex = ($numbersHashInt + mb_ord(mb_substr($ret, 2, 1), 'UTF-8')) % mb_strlen($this->guards);
                $guard = mb_substr($this->guards, $guardIndex, 1);

                $ret .= $guard;
            }
        }

        $halfLength = (int) (mb_strlen($alphabet) / 2);
        while (mb_strlen($ret) < $this->minHashLength) {
            $alphabet = $this->shuffle($alphabet, $alphabet);
            $ret = mb_substr($alphabet, $halfLength) . $ret . mb_substr($alphabet, 0, $halfLength);

            $excess = mb_strlen($ret) - $this->minHashLength;
            if ($excess > 0) {
                $ret = mb_substr($ret, (int) ($excess / 2), $this->minHashLength);
            }
        }

        return $ret;
    }

    public function decode(string $hash): array
    {
        $ret = [];

        if (!($hash = trim($hash))) {
            return $ret;
        }

        $alphabet = $this->alphabet;

        $hashBreakdown = str_replace($this->multiByteSplit($this->guards), ' ', $hash);
        $hashArray = explode(' ', $hashBreakdown);

        $i = 3 === count($hashArray) || 2 === count($hashArray) ? 1 : 0;

        $hashBreakdown = $hashArray[$i];

        if ('' !== $hashBreakdown) {
            $lottery = mb_substr($hashBreakdown, 0, 1);
            $hashBreakdown = mb_substr($hashBreakdown, 1);

            $hashBreakdown = str_replace($this->multiByteSplit($this->seps), ' ', $hashBreakdown);
            $hashArray = explode(' ', $hashBreakdown);

            foreach ($hashArray as $subHash) {
                $alphabet = $this->shuffle($alphabet, mb_substr($lottery . $this->salt . $alphabet, 0, mb_strlen($alphabet)));
                $result = $this->unhash($subHash, $alphabet);
                if ($this->math->greaterThan($result, PHP_INT_MAX)) {
                    $ret[] = $this->math->strval($result);
                } else {
                    $ret[] = $this->math->intval($result);
                }
            }

            if ($this->encode($ret) !== $hash) {
                $ret = [];
            }
        }

        return $ret;
    }

    public function encodeHex(string $str): string
    {
        if (!ctype_xdigit($str)) {
            return '';
        }

        $numbers = trim(chunk_split($str, 12, ' '));
        $numbers = explode(' ', $numbers);

        foreach ($numbers as $i => $number) {
            $numbers[$i] = hexdec('1' . $number);
        }

        return $this->encode(...$numbers);
    }

    public function decodeHex(string $hash): string
    {
        $ret = '';
        $numbers = $this->decode($hash);

        foreach ($numbers as $number) {
            $ret .= mb_substr(dechex($number), 1);
        }

        return $ret;
    }

    /** Shuffle alphabet by given salt. */
    protected function shuffle(string $alphabet, string $salt): string
    {
        $key = $alphabet . ' ' . $salt;

        if (isset($this->shuffledAlphabets[$key])) {
            return $this->shuffledAlphabets[$key];
        }

        $saltLength = mb_strlen($salt);
        $saltArray = $this->multiByteSplit($salt);
        if (!$saltLength) {
            return $alphabet;
        }
        $alphabetArray = $this->multiByteSplit($alphabet);
        for ($i = mb_strlen($alphabet) - 1, $v = 0, $p = 0; $i > 0; $i--, $v++) {
            $v %= $saltLength;
            $p += $int = mb_ord($saltArray[$v], 'UTF-8');
            $j = ($int + $v + $p) % $i;

            $temp = $alphabetArray[$j];
            $alphabetArray[$j] = $alphabetArray[$i];
            $alphabetArray[$i] = $temp;
        }
        $alphabet = implode('', $alphabetArray);
        $this->shuffledAlphabets[$key] = $alphabet;

        return $alphabet;
    }

    /** Hash given input value. */
    protected function hash(string $input, string $alphabet): string
    {
        $hash = '';
        $alphabetLength = mb_strlen($alphabet);

        do {
            $hash = mb_substr($alphabet, $this->math->intval($this->math->mod($input, $alphabetLength)), 1) . $hash;

            $input = $this->math->divide($input, $alphabetLength);
        } while ($this->math->greaterThan($input, 0));

        return $hash;
    }

    /** Unhash given input value. */
    protected function unhash(string $input, string $alphabet): int|string
    {
        $number = 0;
        $inputLength = mb_strlen($input);

        if ($inputLength && $alphabet) {
            $alphabetLength = mb_strlen($alphabet);
            $inputChars = $this->multiByteSplit($input);

            foreach ($inputChars as $char) {
                $position = mb_strpos($alphabet, $char);
                $number = $this->math->multiply($number, $alphabetLength);
                $number = $this->math->add($number, $position);
            }
        }

        return $number;
    }

    /**
     * Get BC Math or GMP extension.
     * @throws \RuntimeException
     */
    protected function getMathExtension(): MathInterface
    {
        if (extension_loaded('gmp')) {
            return new Gmp();
        }

        if (extension_loaded('bcmath')) {
            return new BCMath();
        }

        throw new RuntimeException('Missing math extension for Hashids, install either bcmath or gmp.');
    }

    /**
     * Replace simple use of $this->multiByteSplit with multi byte string.
     * @return array<int, string>
     */
    protected function multiByteSplit(string $string): array
    {
        return preg_split('/(?!^)(?=.)/u', $string) ?: [];
    }
}
