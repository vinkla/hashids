<?php

/*
 * This file is part of Hashids.
 *
 * (c) Ivan Akimov <ivan@barreleye.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hashids;

use Hashids\Math\Bc;
use Hashids\Math\Gmp;
use Hashids\Math\MathInterface;
use RuntimeException;

/**
 * This is the hashids class.
 *
 * @author Ivan Akimov <ivan@barreleye.com>
 * @author Vincent Klaiber <hello@doubledip.se>
 * @author Johnson Page <jwpage@gmail.com>
 */
class Hashids implements HashidsInterface
{
    /**
     * The seps divider.
     *
     * @var float
     */
    const SEP_DIV = 3.5;

    /**
     * The guard divider.
     *
     * @var float
     */
    const GUARD_DIV = 12;

    /**
     * The alphabet string.
     *
     * @var string
     */
    protected $alphabet;

    /**
     * Shuffled alphabets, referenced by alphabet and salt.
     *
     * @var array
     */
    protected $shuffledAlphabets;

    /**
     * The seps string.
     *
     * @var string
     */
    protected $seps = 'cfhistuCFHISTU';

    /**
     * The guards string.
     *
     * @var string
     */
    protected $guards;

    /**
     * The minimum hash length.
     *
     * @var int
     */
    protected $minHashLength;

    /**
     * The salt string.
     *
     * @var string
     */
    protected $salt;

    /**
     * The math class.
     *
     * @var \Hashids\Math\MathInterface
     */
    protected $math;

    /**
     * Create a new hashids instance.
     *
     * @param string $salt
     * @param int $minHashLength
     * @param string $alphabet
     *
     * @throws \Hashids\HashidsException
     */
    public function __construct($salt = '', $minHashLength = 0, $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890')
    {
        $this->salt = \mb_convert_encoding($salt, 'UTF-8', \mb_detect_encoding($salt));
        $this->minHashLength = $minHashLength;
        $alphabet = \mb_convert_encoding($alphabet, 'UTF-8', \mb_detect_encoding($alphabet));
        $this->alphabet = \implode('', \array_unique($this->multiByteSplit($alphabet)));
        $this->math = $this->getMathExtension();

        if (\mb_strlen($this->alphabet) < 16) {
            throw new HashidsException('Alphabet must contain at least 16 unique characters.');
        }

        if (false !== \mb_strpos($this->alphabet, ' ')) {
            throw new HashidsException('Alphabet can\'t contain spaces.');
        }

        $alphabetArray = $this->multiByteSplit($this->alphabet);
        $sepsArray = $this->multiByteSplit($this->seps);
        $this->seps = \implode('', \array_intersect($sepsArray, $alphabetArray));
        $this->alphabet = \implode('', \array_diff($alphabetArray, $sepsArray));
        $this->seps = $this->shuffle($this->seps, $this->salt);

        if (!$this->seps || (\mb_strlen($this->alphabet) / \mb_strlen($this->seps)) > self::SEP_DIV) {
            $sepsLength = (int) \ceil(\mb_strlen($this->alphabet) / self::SEP_DIV);

            if ($sepsLength > \mb_strlen($this->seps)) {
                $diff = $sepsLength - \mb_strlen($this->seps);
                $this->seps .= \mb_substr($this->alphabet, 0, $diff);
                $this->alphabet = \mb_substr($this->alphabet, $diff);
            }
        }

        $this->alphabet = $this->shuffle($this->alphabet, $this->salt);
        $guardCount = (int) \ceil(\mb_strlen($this->alphabet) / self::GUARD_DIV);

        if (\mb_strlen($this->alphabet) < 3) {
            $this->guards = \mb_substr($this->seps, 0, $guardCount);
            $this->seps = \mb_substr($this->seps, $guardCount);
        } else {
            $this->guards = \mb_substr($this->alphabet, 0, $guardCount);
            $this->alphabet = \mb_substr($this->alphabet, $guardCount);
        }
    }

    /**
     * Encode parameters to generate a hash.
     *
     * @param mixed $numbers
     *
     * @return string
     */
    public function encode(...$numbers): string
    {
        $ret = '';

        if (1 === \count($numbers) && \is_array($numbers[0])) {
            $numbers = $numbers[0];
        }

        if (!$numbers) {
            return $ret;
        }

        foreach ($numbers as $number) {
            $isNumber = \ctype_digit((string) $number);

            if (!$isNumber) {
                return $ret;
            }
        }

        $alphabet = $this->alphabet;
        $numbersSize = \count($numbers);
        $numbersHashInt = 0;

        foreach ($numbers as $i => $number) {
            $numbersHashInt += $this->math->intval($this->math->mod($number, $i + 100));
        }

        $lottery = $ret = \mb_substr($alphabet, $numbersHashInt % \mb_strlen($alphabet), 1);
        foreach ($numbers as $i => $number) {
            $alphabet = $this->shuffle($alphabet, \mb_substr($lottery.$this->salt.$alphabet, 0, \mb_strlen($alphabet)));
            $ret .= $last = $this->hash($number, $alphabet);

            if ($i + 1 < $numbersSize) {
                $number %= (\mb_ord($last, 'UTF-8') + $i);
                $sepsIndex = $this->math->intval($this->math->mod($number, \mb_strlen($this->seps)));
                $ret .= \mb_substr($this->seps, $sepsIndex, 1);
            }
        }

        if (\mb_strlen($ret) < $this->minHashLength) {
            $guardIndex = ($numbersHashInt + \mb_ord(\mb_substr($ret, 0, 1), 'UTF-8')) % \mb_strlen($this->guards);

            $guard = \mb_substr($this->guards, $guardIndex, 1);
            $ret = $guard.$ret;

            if (\mb_strlen($ret) < $this->minHashLength) {
                $guardIndex = ($numbersHashInt + \mb_ord(\mb_substr($ret, 2, 1), 'UTF-8')) % \mb_strlen($this->guards);
                $guard = \mb_substr($this->guards, $guardIndex, 1);

                $ret .= $guard;
            }
        }

        $halfLength = (int) (\mb_strlen($alphabet) / 2);
        while (\mb_strlen($ret) < $this->minHashLength) {
            $alphabet = $this->shuffle($alphabet, $alphabet);
            $ret = \mb_substr($alphabet, $halfLength).$ret.\mb_substr($alphabet, 0, $halfLength);

            $excess = \mb_strlen($ret) - $this->minHashLength;
            if ($excess > 0) {
                $ret = \mb_substr($ret, (int) ($excess / 2), $this->minHashLength);
            }
        }

        return $ret;
    }

    /**
     * Decode a hash to the original parameter values.
     *
     * @param string $hash
     *
     * @return array
     */
    public function decode($hash): array
    {
        $ret = [];

        if (!\is_string($hash) || !($hash = \trim($hash))) {
            return $ret;
        }

        $alphabet = $this->alphabet;

        $hashBreakdown = \str_replace($this->multiByteSplit($this->guards), ' ', $hash);
        $hashArray = \explode(' ', $hashBreakdown);

        $i = 3 === \count($hashArray) || 2 === \count($hashArray) ? 1 : 0;

        $hashBreakdown = $hashArray[$i];

        if ('' !== $hashBreakdown) {
            $lottery = \mb_substr($hashBreakdown, 0, 1);
            $hashBreakdown = \mb_substr($hashBreakdown, 1);

            $hashBreakdown = \str_replace($this->multiByteSplit($this->seps), ' ', $hashBreakdown);
            $hashArray = \explode(' ', $hashBreakdown);

            foreach ($hashArray as $subHash) {
                $alphabet = $this->shuffle($alphabet, \mb_substr($lottery.$this->salt.$alphabet, 0, \mb_strlen($alphabet)));
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

    /**
     * Encode hexadecimal values and generate a hash string.
     *
     * @param string $str
     *
     * @return string
     */
    public function encodeHex($str): string
    {
        if (!\ctype_xdigit((string) $str)) {
            return '';
        }

        $numbers = \trim(chunk_split($str, 12, ' '));
        $numbers = \explode(' ', $numbers);

        foreach ($numbers as $i => $number) {
            $numbers[$i] = \hexdec('1'.$number);
        }

        return $this->encode(...$numbers);
    }

    /**
     * Decode a hexadecimal hash.
     *
     * @param string $hash
     *
     * @return string
     */
    public function decodeHex($hash): string
    {
        $ret = '';
        $numbers = $this->decode($hash);

        foreach ($numbers as $i => $number) {
            $ret .= \mb_substr(dechex($number), 1);
        }

        return $ret;
    }

    /**
     * Shuffle alphabet by given salt.
     *
     * @param string $alphabet
     * @param string $salt
     *
     * @return string
     */
    protected function shuffle($alphabet, $salt): string
    {
        $key = $alphabet.' '.$salt;

        if (isset($this->shuffledAlphabets[$key])) {
            return $this->shuffledAlphabets[$key];
        }

        $saltLength = \mb_strlen($salt);
        $saltArray = $this->multiByteSplit($salt);
        if (!$saltLength) {
            return $alphabet;
        }
        $alphabetArray = $this->multiByteSplit($alphabet);
        for ($i = \mb_strlen($alphabet) - 1, $v = 0, $p = 0; $i > 0; $i--, $v++) {
            $v %= $saltLength;
            $p += $int = \mb_ord($saltArray[$v], 'UTF-8');
            $j = ($int + $v + $p) % $i;

            $temp = $alphabetArray[$j];
            $alphabetArray[$j] = $alphabetArray[$i];
            $alphabetArray[$i] = $temp;
        }
        $alphabet = \implode('', $alphabetArray);
        $this->shuffledAlphabets[$key] = $alphabet;

        return $alphabet;
    }

    /**
     * Hash given input value.
     *
     * @param string $input
     * @param string $alphabet
     *
     * @return string
     */
    protected function hash($input, $alphabet): string
    {
        $hash = '';
        $alphabetLength = \mb_strlen($alphabet);

        do {
            $hash = \mb_substr($alphabet, $this->math->intval($this->math->mod($input, $alphabetLength)), 1).$hash;

            $input = $this->math->divide($input, $alphabetLength);
        } while ($this->math->greaterThan($input, 0));

        return $hash;
    }

    /**
     * Unhash given input value.
     *
     * @param string $input
     * @param string $alphabet
     *
     * @return int
     */
    protected function unhash($input, $alphabet)
    {
        $number = 0;
        $inputLength = \mb_strlen($input);

        if ($inputLength && $alphabet) {
            $alphabetLength = \mb_strlen($alphabet);
            $inputChars = $this->multiByteSplit($input);

            foreach ($inputChars as $char) {
                $position = \mb_strpos($alphabet, $char);
                $number = $this->math->multiply($number, $alphabetLength);
                $number = $this->math->add($number, $position);
            }
        }

        return $number;
    }

    /**
     * Get BC Math or GMP extension.
     *
     * @codeCoverageIgnore
     *
     * @throws \RuntimeException
     *
     * @return \Hashids\Math\MathInterface
     */
    protected function getMathExtension(): MathInterface
    {
        if (\extension_loaded('gmp')) {
            return new Gmp();
        }

        if (\extension_loaded('bcmath')) {
            return new Bc();
        }

        throw new RuntimeException('Missing BC Math or GMP extension.');
    }

    /**
     * Replace simple use of $this->multiByteSplit with multi byte string.
     *
     * @param $string
     *
     * @return array|string[]
     */
    protected function multiByteSplit($string): array
    {
        return \preg_split('/(?!^)(?=.)/u', $string) ?: [];
    }
}
