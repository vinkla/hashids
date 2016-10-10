<?php

/*
    
    Hashids
    http://hashids.org/php
    (c) 2013 Ivan Akimov
    
    https://github.com/ivanakimov/hashids.php
    hashids may be freely distributed under the MIT license.
    
*/

namespace Hashids;

class Hashids implements HashGenerator
{
    const VERSION = '1.0.6';

    /* internal settings */

    const MIN_ALPHABET_LENGTH = 16;
    const SEP_DIV = 3.5;
    const GUARD_DIV = 12;

    /* error messages */

    const E_ALPHABET_LENGTH = 'alphabet must contain at least %d unique characters';
    const E_ALPHABET_SPACE = 'alphabet cannot contain spaces';

    /* set at constructor */

    private $_alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    private $_seps = 'cfhistuCFHISTU';
    private $_min_hash_length = 0;
    private $_math_functions = array();
    private $_max_int_value = 1000000000;

    public function __construct($salt = '', $min_hash_length = 0, $alphabet = '')
    {

        /* if either math precision library is present, raise $this->_max_int_value */

        if (function_exists('gmp_add')) {
            $this->_math_functions['add'] = 'gmp_add';
            $this->_math_functions['div'] = 'gmp_div';
            $this->_math_functions['str'] = 'gmp_strval';
        } elseif (function_exists('bcadd')) {
            $this->_math_functions['add'] = 'bcadd';
            $this->_math_functions['div'] = 'bcdiv';
            $this->_math_functions['str'] = 'strval';
        }

        $this->_lower_max_int_value = $this->_max_int_value;
        if ($this->_math_functions) {
            $this->_max_int_value = PHP_INT_MAX;
        }

        /* handle parameters */

        $this->_salt = $salt;

        if ((int) $min_hash_length > 0) {
            $this->_min_hash_length = (int) $min_hash_length;
        }

        if ($alphabet) {
            $this->_alphabet = implode('', array_unique(str_split($alphabet)));
        }

        if (strlen($this->_alphabet) < self::MIN_ALPHABET_LENGTH) {
            throw new \Exception(sprintf(self::E_ALPHABET_LENGTH, self::MIN_ALPHABET_LENGTH));
        }

        if (is_int(strpos($this->_alphabet, ' '))) {
            throw new \Exception(self::E_ALPHABET_SPACE);
        }

        $alphabet_array = str_split($this->_alphabet);
        $seps_array = str_split($this->_seps);

        $this->_seps = implode('', array_intersect($alphabet_array, $seps_array));
        $this->_alphabet = implode('', array_diff($alphabet_array, $seps_array));
        $this->_seps = $this->_consistent_shuffle($this->_seps, $this->_salt);

        if (!$this->_seps || (strlen($this->_alphabet) / strlen($this->_seps)) > self::SEP_DIV) {
            $seps_length = (int) ceil(strlen($this->_alphabet) / self::SEP_DIV);

            if ($seps_length == 1) {
                ++$seps_length;
            }

            if ($seps_length > strlen($this->_seps)) {
                $diff = $seps_length - strlen($this->_seps);
                $this->_seps .= substr($this->_alphabet, 0, $diff);
                $this->_alphabet = substr($this->_alphabet, $diff);
            } else {
                $this->_seps = substr($this->_seps, 0, $seps_length);
            }
        }

        $this->_alphabet = $this->_consistent_shuffle($this->_alphabet, $this->_salt);
        $guard_count = (int) ceil(strlen($this->_alphabet) / self::GUARD_DIV);

        if (strlen($this->_alphabet) < 3) {
            $this->_guards = substr($this->_seps, 0, $guard_count);
            $this->_seps = substr($this->_seps, $guard_count);
        } else {
            $this->_guards = substr($this->_alphabet, 0, $guard_count);
            $this->_alphabet = substr($this->_alphabet, $guard_count);
        }
    }

    public function encode()
    {
        $ret = '';
        $numbers = func_get_args();

        if (func_num_args() == 1 && is_array(func_get_arg(0))) {
            $numbers = $numbers[0];
        }

        if (!$numbers) {
            return $ret;
        }

        foreach ($numbers as $number) {
            $is_number = ctype_digit((string) $number);

            if (!$is_number || $number < 0 || $number > $this->_max_int_value) {
                return $ret;
            }
        }

        return $this->_encode($numbers);
    }

    public function decode($hash)
    {
        $ret = array();

        if (!is_string($hash) || !($hash = trim($hash))) {
            return $ret;
        }

        return $this->_decode($hash, $this->_alphabet);
    }

    public function encode_hex($str)
    {
        if (!ctype_xdigit((string) $str)) {
            return '';
        }

        $numbers = trim(chunk_split($str, 12, ' '));
        $numbers = explode(' ', $numbers);

        foreach ($numbers as $i => $number) {
            $numbers[$i] = hexdec('1'.$number);
        }

        return call_user_func_array(array($this, 'encode'), $numbers);
    }

    public function decode_hex($hash)
    {
        $ret = '';
        $numbers = $this->decode($hash);

        foreach ($numbers as $i => $number) {
            $ret .= substr(dechex($number), 1);
        }

        return $ret;
    }

    public function get_max_int_value()
    {
        return $this->_max_int_value;
    }

    private function _encode(array $numbers)
    {
        $alphabet = $this->_alphabet;
        $numbers_size = sizeof($numbers);
        $numbers_hash_int = 0;

        foreach ($numbers as $i => $number) {
            $numbers_hash_int += ($number % ($i + 100));
        }

        $lottery = $ret = $alphabet[$numbers_hash_int % strlen($alphabet)];
        foreach ($numbers as $i => $number) {
            $alphabet = $this->_consistent_shuffle($alphabet, substr($lottery.$this->_salt.$alphabet, 0, strlen($alphabet)));
            $ret .= $last = $this->_hash($number, $alphabet);

            if ($i + 1 < $numbers_size) {
                $number %= (ord($last) + $i);
                $seps_index = $number % strlen($this->_seps);
                $ret .= $this->_seps[$seps_index];
            }
        }

        if (strlen($ret) < $this->_min_hash_length) {
            $guard_index = ($numbers_hash_int + ord($ret[0])) % strlen($this->_guards);

            $guard = $this->_guards[$guard_index];
            $ret = $guard.$ret;

            if (strlen($ret) < $this->_min_hash_length) {
                $guard_index = ($numbers_hash_int + ord($ret[2])) % strlen($this->_guards);
                $guard = $this->_guards[$guard_index];

                $ret .= $guard;
            }
        }

        $half_length = (int) (strlen($alphabet) / 2);
        while (strlen($ret) < $this->_min_hash_length) {
            $alphabet = $this->_consistent_shuffle($alphabet, $alphabet);
            $ret = substr($alphabet, $half_length).$ret.substr($alphabet, 0, $half_length);

            $excess = strlen($ret) - $this->_min_hash_length;
            if ($excess > 0) {
                $ret = substr($ret, $excess / 2, $this->_min_hash_length);
            }
        }

        return $ret;
    }

    private function _decode($hash, $alphabet)
    {
        $ret = array();

        $hash_breakdown = str_replace(str_split($this->_guards), ' ', $hash);
        $hash_array = explode(' ', $hash_breakdown);

        $i = 0;
        if (sizeof($hash_array) == 3 || sizeof($hash_array) == 2) {
            $i = 1;
        }

        $hash_breakdown = $hash_array[$i];
        if (isset($hash_breakdown[0])) {
            $lottery = $hash_breakdown[0];
            $hash_breakdown = substr($hash_breakdown, 1);

            $hash_breakdown = str_replace(str_split($this->_seps), ' ', $hash_breakdown);
            $hash_array = explode(' ', $hash_breakdown);

            foreach ($hash_array as $sub_hash) {
                $alphabet = $this->_consistent_shuffle($alphabet, substr($lottery.$this->_salt.$alphabet, 0, strlen($alphabet)));
                $ret[] = (int) $this->_unhash($sub_hash, $alphabet);
            }

            if ($this->_encode($ret) != $hash) {
                $ret = array();
            }
        }

        return $ret;
    }

    private function _consistent_shuffle($alphabet, $salt)
    {
        $salt_length = strlen($salt);
        if (!$salt_length) {
            return $alphabet;
        }

        for ($i = strlen($alphabet) - 1, $v = 0, $p = 0; $i > 0; $i--, $v++) {
            $v %= $salt_length;
            $p += $int = ord($salt[$v]);
            $j = ($int + $v + $p) % $i;

            $temp = $alphabet[$j];
            $alphabet[$j] = $alphabet[$i];
            $alphabet[$i] = $temp;
        }

        return $alphabet;
    }

    private function _hash($input, $alphabet)
    {
        $hash = '';
        $alphabet_length = strlen($alphabet);

        do {
            $hash = $alphabet[$input % $alphabet_length].$hash;
            if ($input > $this->_lower_max_int_value && $this->_math_functions) {
                $input = $this->_math_functions['str']($this->_math_functions['div']($input, $alphabet_length));
            } else {
                $input = (int) ($input / $alphabet_length);
            }
        } while ($input);

        return $hash;
    }

    private function _unhash($input, $alphabet)
    {
        $number = 0;
        $input_length = strlen($input);

        if ($input_length && $alphabet) {
            $alphabet_length = strlen($alphabet);
            $input_chars = str_split($input);

            foreach ($input_chars as $i => $char) {
                $pos = strpos($alphabet, $char);
                if ($this->_math_functions) {
                    $number = $this->_math_functions['str']($this->_math_functions['add']($number, $pos * pow($alphabet_length, ($input_length - $i - 1))));
                } else {
                    $number += $pos * pow($alphabet_length, ($input_length - $i - 1));
                }
            }
        }

        return $number;
    }
}
