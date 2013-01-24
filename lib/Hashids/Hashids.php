<?php

/*
	Hashids may be freely distributed under the MIT license.
	Documentation: http://www.hashids.org/php/
	Source: https://github.com/ivanakimov/hashids.php
*/

namespace Hashids;

class Hashids {
	
	const VERSION = '0.2.1';
	const MIN_ALPHABET_LENGTH = 16;
	const SEP_DIV = 3.5;
	const GUARD_DIV = 12;
	
	const E_ALPHABET_LENGTH = 'alphabet must contain at least %d unique characters';
	const E_ALPHABET_SPACE = 'alphabet cannot contain spaces';
	
	private $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	private $seps = 'cCsSfFhHuUiItT';
	private $min_hash_length = 0;
	
	function __construct($salt = '', $min_hash_length = 0, $alphabet = '') {
		
		$this->salt = $salt;
		
		if ((int)$min_hash_length > 0)
			$this->min_hash_length = (int)$min_hash_length;
		
		if ($alphabet)
			$this->alphabet = implode('', array_unique(str_split($alphabet)));
		
		if (strlen($this->alphabet) < self::MIN_ALPHABET_LENGTH)
			throw new Exception(sprintf(self::E_ALPHABET_LENGTH, self::MIN_ALPHABET_LENGTH));
		
		if (is_int(strpos($this->alphabet, ' ')))
			throw new Exception(self::E_ALPHABET_SPACE);
		
		$alphabet_array = str_split($this->alphabet);
		$seps_array = str_split($this->seps);
		
		$this->seps = implode('', array_intersect($alphabet_array, $seps_array));
		$this->alphabet = implode('', array_diff($alphabet_array, $seps_array));
		$this->seps = $this->_consistent_shuffle($this->seps, $this->salt);
		
		if (!$this->seps || (strlen($this->alphabet) / strlen($this->seps)) > self::SEP_DIV) {
			
			$seps_length = (int)ceil(strlen($this->alphabet) / self::SEP_DIV);
			
			if ($seps_length == 1)
				$seps_length++;
			
			if ($seps_length > strlen($this->seps)) {
				
				$diff = $seps_length - strlen($this->seps);
				$this->seps .= substr($this->alphabet, 0, $diff);
				$this->alphabet = substr($this->alphabet, $diff);
				
			} else
				$this->seps = substr($this->seps, 0, $seps_length);
			
		}
		
		$this->alphabet = $this->_consistent_shuffle($this->alphabet, $this->salt);
		$guard_count = (int)ceil(strlen($this->alphabet) / self::GUARD_DIV);
		
		if (strlen($this->alphabet) < 3) {
			$this->guards = substr($this->seps, 0, $guard_count);
			$this->seps = substr($this->seps, $guard_count);
		} else {
			$this->guards = substr($this->alphabet, 0, $guard_count);
			$this->alphabet = substr($this->alphabet, $guard_count);
		}
		
	}
	
	function encrypt() {
		
		$ret = '';
		$numbers = func_get_args();
		
		if (!$numbers)
			return $ret;
		
		foreach ($numbers as $number) {
			if (!is_int($number) || $number < 0)
				return $ret;
		}
		
		return $this->_encode($numbers, $this->alphabet);
		
	}
	
	function decrypt($hash) {
		
		$ret = array();
		
		if (!$hash || !is_string($hash) || !trim($hash))
			return $ret;
		
		return $this->_decode(trim($hash), $this->alphabet);
		
	}
	
	private function _encode(array $numbers, $alphabet) {
		
		$numbers_size = sizeof($numbers);
		$numbers_sum = array_sum($numbers);
		$lottery = $ret = $alphabet[$numbers_sum % strlen($alphabet)];
		
		foreach ($numbers as $i => $number) {
			
			$alphabet = $this->_consistent_shuffle($alphabet, substr($lottery . $this->salt . $alphabet, 0, strlen($alphabet)));
			$ret .= $last = $this->_hash($number, $alphabet);
			
			if ($i + 1 < $numbers_size) {
				$seps_index = ($number + ord($last) + $i) % strlen($this->seps);
				$ret .= $this->seps[$seps_index];
			}
			
		}
		
		if (strlen($ret) < $this->min_hash_length) {
			
			$guard_index = ($numbers_sum + ord($ret[0])) % strlen($this->guards);
			
			$guard = $this->guards[$guard_index];
			$ret = $guard . $ret;
			
			if (strlen($ret) < $this->min_hash_length) {
				
				$guard_index = ($numbers_sum + ord($ret[2])) % strlen($this->guards);
				$guard = $this->guards[$guard_index];
				
				$ret .= $guard;
				
			}
			
		}
		
		$half_length = (int)(strlen($alphabet) / 2);
		while (strlen($ret) < $this->min_hash_length) {
			
			$alphabet = $this->_consistent_shuffle($alphabet, $alphabet);
			$ret = substr($alphabet, $half_length) . $ret . substr($alphabet, 0, $half_length);
			
			$excess = strlen($ret) - $this->min_hash_length;
			if ($excess > 0)
				$ret = substr($ret, $excess / 2, $this->min_hash_length);
			
		}
		
		return $ret;
		
	}
	
	private function _decode($hash, $alphabet) {
		
		$ret = array();
		
		$hash_breakdown = str_replace(str_split($this->guards), ' ', $hash);
		$hash_array = explode(' ', $hash_breakdown);
		
		$i = 0;
		if (sizeof($hash_array) == 3 || sizeof($hash_array) == 2)
			$i = 1;
		
		$hash_breakdown = $hash_array[$i];
		if (isset($hash_breakdown[0])) {
			
			$lottery = $hash_breakdown[0];
			$hash_breakdown = substr($hash_breakdown, 1);
			
			$hash_breakdown = str_replace(str_split($this->seps), ' ', $hash_breakdown);
			$hash_array = explode(' ', $hash_breakdown);
			
			foreach ($hash_array as $sub_hash) {
				$alphabet = $this->_consistent_shuffle($alphabet, substr($lottery . $this->salt . $alphabet, 0, strlen($alphabet)));
				$ret[] = $this->_unhash($sub_hash, $alphabet);
			}
			
			if (call_user_func_array(array($this, 'encrypt'), $ret) != $hash)
				$ret = array();
			
		}
		
		return $ret;
		
	}
	
	private function _consistent_shuffle($alphabet, $salt) {
		
		if (!strlen($salt))
			return $alphabet;
		
		for ($i = strlen($alphabet) - 1, $v = 0, $p = 0; $i > 0; $i--, $v++) {
			
			$v %= strlen($salt);
			$p += $int = ord($salt[$v]);
			$j = ($int + $v + $p) % $i;
			
			$temp = $alphabet[$j];
			$alphabet[$j] = $alphabet[$i];
			$alphabet[$i] = $temp;
			
		}
		
		return $alphabet;
		
	}
	
	private function _hash($input, $alphabet) {
		
		$hash = '';
		$alphabet_length = strlen($alphabet);
		
		do {
			$hash = $alphabet[$input % $alphabet_length] . $hash;
			$input = (int)($input / $alphabet_length);
		} while ($input);
		
		return $hash;
		
	}
	
	private function _unhash($input, $alphabet) {
		
		$number = 0;
		if (strlen($input) && $alphabet) {
			
			$alphabet_length = strlen($alphabet);
			$input_chars = str_split($input);
			
			foreach ($input_chars as $i => $char) {
				$pos = strpos($alphabet, $char);
				$number += $pos * pow($alphabet_length, (strlen($input) - $i - 1));
			}
			
		}
		
		return $number;
		
	}
	
}
