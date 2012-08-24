<?php

/*
	hashids
	http://www.hashids.org/php/
	(c) 2012 Ivan Akimov
	
	https://github.com/ivanakimov/hashids.php
	hashids may be freely distributed under the MIT license.
*/

class hashids {
	
	public $version = '0.1.2';
	
	private $alphabet = 'xcS4F6h89aUbidefI7fjkyunopqrsgCYE5GHTCKLHMtARXz';
	private $primes = [2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43];
	private $min_hash_length = 0;
	
	function __construct($salt = '', $min_hash_length = 0, $alphabet = '') {
		
		$this->salt = $salt;
		
		if ((int)$min_hash_length > 0)
			$this->min_hash_length = (int)$min_hash_length;
		
		if ($alphabet)
			$this->alphabet = implode('', array_unique(str_split($alphabet)));
		
		if (strlen($this->alphabet) < 4)
			throw new Exception('Alphabet must contain at least 4 unique characters');
		
		$this->seps = [];
		foreach ($this->primes as $i => $prime) {
			
			if (isset($this->alphabet[$prime - 1])) {
				$this->seps[] = $char = $this->alphabet[$prime - 1];
				$this->alphabet = str_replace($char, ' ', $this->alphabet);
			} else
				break;
			
		}
		
		$this->alphabet = str_replace(' ', '', $this->alphabet);
		$this->guards = [];
		
		foreach ([0, 4, 8, 12] as $index) {
			if (isset($this->seps[$index])) {
				$this->guards[] = $this->seps[$index];
				unset($this->seps[$index]);
			}
		}
		
		$this->alphabet = $this->_consistent_shuffle($this->alphabet, $this->salt);
		$this->seps = array_merge($this->seps);
		
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
		
		return $this->_encode($numbers, $this->alphabet, $this->salt, $this->min_hash_length);
		
	}
	
	function decrypt($hash) {
		
		$ret = [];
		
		if (!$hash || !is_string($hash))
			return $ret;
		
		return $this->_decode($hash);
		
	}
	
	private function _encode(array $numbers, $alphabet, $salt, $min_hash_length = 0) {
		
		$ret = '';
		
		$func = __FUNCTION__;
		$seps = str_split($this->_consistent_shuffle($this->seps, $numbers));
		
		foreach ($numbers as $i => $number) {
			
			if (!$i) {
				
				$lottery_salt = implode('-', $numbers);
				foreach ($numbers as $sub_number)
					$lottery_salt .= '-' . ($sub_number + 1) * 2;
				
				$lottery = $this->_consistent_shuffle($alphabet, $lottery_salt);
				$ret .= $lottery_char = $lottery[0];
				
				$alphabet = $lottery_char . str_replace($lottery_char, '', $alphabet);
				
			}
			
			$alphabet = $this->_consistent_shuffle($alphabet, ord($lottery_char) & 12345 . $salt);
			$ret .= $this->_hash($number, $alphabet);
			
			if ($i + 1 < sizeof($numbers)) {
				$seps_index = ($number + $i) % sizeof($seps);
				$ret .= $seps[$seps_index];
			}
			
		}
		
		if (strlen($ret) < $min_hash_length) {
			
			$first_index = 0;
			foreach ($numbers as $i => $number)
				$first_index += ($i + 1) * $number;
			
			$guard_index = $first_index % sizeof($this->guards);
			$guard = $this->guards[$guard_index];
			
			$ret = $guard . $ret;
			if (strlen($ret) < $min_hash_length) {
				
				$guard_index = ($guard_index + strlen($ret)) % sizeof($this->guards);
				$guard = $this->guards[$guard_index];
				
				$ret .= $guard;
				
			}
			
		}
		
		while (strlen($ret) < $min_hash_length) {
			
			$pad_array = [ord($alphabet[1]), ord($alphabet[0])];
			
			$pad_left = $this->$func($pad_array, $alphabet, $salt);
			$pad_right = $this->$func($pad_array, $alphabet, implode('', $pad_array));
			
			$ret = $pad_left . $ret . $pad_right;
			$excess = strlen($ret) - $min_hash_length;
			
			if ($excess > 0)
				$ret = substr($ret, $excess / 2, $min_hash_length);
			
			$alphabet = $this->_consistent_shuffle($alphabet, $salt . $ret);
			
		}
		
		return $ret;
		
	}
	
	private function _decode($hash) {
		
		$ret = [];
		
		if ($hash) {
			
			$original_hash = $hash;
			
			$hash = str_replace($this->guards, ' ', $hash);
			$hash_explode = explode(' ', $hash);
			
			$i = 0;
			if (sizeof($hash_explode) == 3 || sizeof($hash_explode) == 2)
				$i = 1;
			
			$hash = $hash_explode[$i];
			
			$hash = str_replace($this->seps, ' ', $hash);
			$hash_array = explode(' ', $hash);
			
			foreach ($hash_array as $i => $sub_hash) {
				if (strlen($sub_hash)) {
					
					if (!$i) {
						$lottery_char = $hash[0];
						$sub_hash = substr($sub_hash, 1);
						$alphabet = $lottery_char . str_replace($lottery_char, '', $this->alphabet);
					}
					
					if (isset($alphabet) && isset($lottery_char)) {
						$alphabet = $this->_consistent_shuffle($alphabet, ord($lottery_char) & 12345 . $this->salt);
						$ret[] = $this->_unhash($sub_hash, $alphabet);
					}
					
				}
			}
			
			if (call_user_func_array([$this, 'encrypt'], $ret) != $original_hash)
				$ret = [];
			
		}
		
		return $ret;
		
	}
	
	private function _consistent_shuffle($alphabet, $salt) {
		
		$ret = '';
		$func = __FUNCTION__;
		
		if (is_array($alphabet))
			$alphabet = implode('', $alphabet);
		
		if (is_array($salt))
			$salt = implode('', $salt);
		
		if ($alphabet) {
			
			$alphabet_array = str_split($alphabet);
			$salt_array = str_split($salt);
			
			$sorting_array = [];
			foreach ($salt_array as $char)
				$sorting_array[] = ord($char);
			
			foreach ($sorting_array as $i => $int) {
				
				$add = true;
				for ($k = $i, $j = sizeof($sorting_array) + $i - 1; $k != $j; $k++) {
					
					$next_index = ($k + 1) % (sizeof($sorting_array));
					
					if ($add)
						$sorting_array[$i] += $sorting_array[$next_index] + ($k * $i);
					else
						$sorting_array[$i] -= $sorting_array[$next_index];
					
					$add = !$add;
					
				}
				
				$sorting_array[$i] = abs($sorting_array[$i]);
				
			}
			
			$i = 0;
			while ($alphabet_array) {
				
				$alphabet_size = sizeof($alphabet_array);
				$pos = $sorting_array[$i];
				
				if ($pos >= $alphabet_size)
					$pos = $pos % $alphabet_size;
				
				$ret .= $alphabet_array[$pos];
				unset($alphabet_array[$pos]);
				$alphabet_array = array_merge($alphabet_array);
				
				$i++;
				$i %= sizeof($sorting_array);
				
			}
			
		}
		
		return $ret;
		
	}
	
	private function _hash($input, $alphabet) {
		
		$ret = '';
		$alphabet_length = strlen($alphabet);
		
		do {
			$rem = $input % $alphabet_length;
			$input = (int)($input / $alphabet_length);
			$ret = $alphabet[$rem] . $ret;
		} while ($input);
		
		return $ret;
		
	}
	
	private function _unhash($input, $alphabet) {
		
		$ret = 0;
		
		if (strlen($input) && $alphabet) {
			
			$alphabet_length = strlen($alphabet);
			$input_chars = str_split($input);
			
			foreach ($input_chars as $i => $char) {
				$pos = strpos($alphabet, $char);
				$ret += $pos * pow($alphabet_length, (strlen($input) - $i - 1));
			}
			
		}
		
		return $ret;
		
	}
	
}
