<?php
	
	// hashids 0.1.0
	// (c) 2012 Ivan Akimov
	// https://github.com/ivanakimov/hash-ids
	// hash-ids may be freely distributed under the MIT license.
	
	class hashids {
		
		private $salt;
		private $alphabet = '-023456789abdegjklmnopqrtvwxyzABDEGJKLMNOPQRTVWXYZ';
		private $separators = '1fFuUsSiIcChH';
		
		function __construct($salt = '') {
			
			$this->salt = $salt;
			$this->alphabet_length = strlen($this->alphabet);
			
			if (strlen($this->salt))
				$this->alphabet = $this->_shuffle($this->alphabet, $this->salt);
			
		}
		
		function encode() {
			return call_user_func_array([$this, '_encode'], func_get_args());
		}
		
		function decode($hash) {
			return $this->_decode($hash);
		}
		
		private function _encode() {
			
			$ret = '';
			$alphabet = $this->alphabet;
			
			$args = func_get_args();
			foreach ($args as $i => $arg) {
				
				if ($arg < 0) {
					$ret = '';
					break;
				}
				
				if ($i) {
					$params = array_slice($args, 0, $i);
					$ret .= call_user_func_array([$this, '_get_separator'], $params);
				}
				
				$hash = $this->_hash($arg, $alphabet);
				$ret .= $hash;
				
				$alphabet = $this->_shuffle($alphabet, $this->salt . $hash);
				
			}
			
			return $ret;
			
		}
		
		private function _decode($hash) {
			
			$ret = [];
			$alphabet = $this->alphabet;
			
			$hash = trim($hash);
			if ($hash) {
				
				$separator_array = str_split($this->separators);
				$hash_spaces = str_replace($separator_array, ' ', $hash);
				$hash_array = explode(' ', $hash_spaces);
				
				foreach ($hash_array as $i => $sub_hash) {
					
					if ($sub_hash) {
						
						$id = $this->_unhash($sub_hash, $alphabet);
						$ret[] = $id;
						
						if ($i + 1 < sizeof($hash_array))
							$alphabet = $this->_shuffle($alphabet, $this->salt . $sub_hash);
						
					}
					
				}
				
			}
			
			$validate = call_user_func_array([$this, 'encode'], $ret);
			if ($validate != $hash)
				$ret = [];
			
			return $ret;
			
		}
		
		private function _shuffle($alphabet, $salt) {
			
			$ret = '';
			$alphabet_array = str_split($this->alphabet);
			$sorting_array = str_split(md5($salt));
			
			$i = 0;
			while ($alphabet_array) {
				
				$alphabet_size = sizeof($alphabet_array);
				$pos = hexdec($sorting_array[$i]);
				
				if ($pos >= $alphabet_size)
					$pos = ($alphabet_size - 1) % $pos;
				
				$ret .= $alphabet_array[$pos];
				unset($alphabet_array[$pos]);
				$alphabet_array = array_merge($alphabet_array);
				
				$i++;
				$i %= sizeof($sorting_array);
				
			}
			
			return $ret;
			
		}
		
		private function _hash($input, $alphabet) {
			
			$ret = '';
			$alphabet_length = strlen($alphabet);
			
			do {
				$rem = $input % $alphabet_length;
				$ret = $alphabet[$rem] . $ret;
			} while ((int)$input = $input / $alphabet_length);
			
			return $ret;
			
		}
		
		private function _unhash($input, $alphabet) {
			
			$ret = 0;
			$alphabet_length = strlen($alphabet);
			$input_chars = str_split($input);
			
			foreach ($input_chars as $i => $char) {
				$pos = strpos($alphabet, $char);
				$ret += $pos * pow($alphabet_length, (strlen($input) - $i - 1));
			}
			
			return $ret;
			
		}
		
		private function _get_separator() {
			
			$args = func_get_args();
			$sum = $this->alphabet_length;
			
			foreach ($args as $arg)
				$sum += $arg;
			
			$i = $sum % (strlen($this->separators) - 1);
			return $this->separators[$i];
			
		}
		
	}
	