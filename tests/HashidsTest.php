<?php

/* phpunit tests/HashidsTest.php */

class HashidsTest extends \PHPUnit_Framework_TestCase {
	
	private $hashids = null;
	private $salt = 'this is my salt';
	private $min_hash_length = 1000;
	private $custom_alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	
	private $max_id = 75; /* set higher to test locally */
	
	public function __construct() {
		
		$this->hashids = new Hashids\Hashids($this->salt);
		$this->hashids_min_length = new Hashids\Hashids($this->salt, $this->min_hash_length);
		$this->hashids_alphabet = new Hashids\Hashids($this->salt, 0, $this->custom_alphabet);
		
	}
	
	public function testCollisions() {
		
		foreach (array(
			$this->hashids,
			$this->hashids_min_length,
			$this->hashids_alphabet
		) as $hashids) {
			
			$hashes = array();
			
			/* encode one number like [123] */
			
			for ($i = 0; $i != $this->max_id; $i++) {
				$hashes[] = $hashids->encode($i);
			}
			
			$unique_array = array_unique($hashes);
			$this->assertEquals(0, sizeof($hashes) - sizeof($unique_array));
			
		}
		
	}
	
	public function testMultiCollisions() {
		
		foreach (array(
			$this->hashids,
			$this->hashids_min_length,
			$this->hashids_alphabet
		) as $hashids) {
			
			$hashes = array();
			$max_id = (int)($this->max_id / 3);
			
			/* encode multiple numbers like [1, 2, 3] */
			
			for ($i = 0; $i != $max_id; $i++) {
				for ($j = 0; $j != $max_id; $j++) {
					for ($k = 0; $k != $max_id; $k++) {
						$hashes[] = $hashids->encode($i, $j, $k);
					}
				}
			}
			
			$unique_array = array_unique($hashes);
			$this->assertEquals(0, sizeof($hashes) - sizeof($unique_array));
			
		}
		
	}
	
	public function testMinHashLength() {
		
		$hashes = array();
		
		for ($i = 0; $i != $this->max_id; $i++) {
			
			$hash = $this->hashids_min_length->encode($i);
			if (strlen($hash) < $this->min_hash_length) {
				$hashes[] = $hash;
			}
			
		}
		
		$this->assertEquals(0, sizeof($hashes));
		
	}
	
	public function testRandomHashesDecoding() {
		
		$corrupt = $hashes = array();
		
		for ($i = 0; $i != $this->max_id; $i++) {
			
			/* create a random hash */
			
			$random_hash = substr(md5(microtime()), rand(0, 10), rand(3, 12));
			if ($i % 2 == 0) {
				$random_hash = strtoupper($random_hash);
			}
			
			/* decode it; check that it's empty */
			
			$numbers = $this->hashids->decode($random_hash);
			if ($numbers) {
				
				/* could've accidentally generated correct hash, try to encode */
				
				$hash = call_user_func_array(array($this->hashids, 'encode'), $numbers);
				if ($hash != $random_hash) {
					$corrupt[] = $random_hash;
				}
				
			}
			
		}
		
		$this->assertEquals(0, sizeof($corrupt));
		
	}
	
	public function testCustomAlphabet() {
		
		$hashes = array();
		$alphabet_array = str_split($this->custom_alphabet);
		
		for ($i = 0; $i != $this->max_id; $i++) {
			
			$hash = $this->hashids_alphabet->encode($i);
			$hash_array = str_split($hash);
			
			if (array_diff($hash_array, $alphabet_array)) {
				$hashes[] = $hash;
			}
			
		}
		
		$this->assertEquals(0, sizeof($hashes));
		
	}
	
	public function testBigValues() {
		
		$hashes = array();
		$max_int_value = $this->hashids->get_max_int_value();
		
		for ($i = $this->hashids->get_max_int_value(), $j = $i - $this->max_id; $i != $j; $i--) {
			
			$hash = $this->hashids->encode($i);
			$numbers = $this->hashids->decode($hash);
			
			if (!$numbers || $i != $numbers[0]) {
				$hashes[] = $hash;
			}
			
		}
		
		$this->assertEquals(0, sizeof($hashes));
		
	}
	
	public function testOutOfBoundsValue() {
		$hash = $this->hashids->encode($this->hashids->get_max_int_value() + 1);
		$this->assertEquals('', $hash);
	}
	
	public function testNegativeValue() {
		$hash = $this->hashids->encode(-1);
		$this->assertEquals('', $hash);
	}
	
	public function testEncodingEmptyArray() {
		$hash = $this->hashids->encode(array());
		$this->assertEquals('', $hash);
	}
	
	public function testEncodingWithoutParams() {
		$hash = $this->hashids->encode();
		$this->assertEquals('', $hash);
	}
	
	public function testEncodingDecodingHex() {
		
		$testValue = '3ade68b1000fff';
		
		$id = $this->hashids->encode_hex($testValue);
		$this->assertTrue((bool)$id);
		
		$hex = $this->hashids->decode_hex($id);
		$this->assertEquals($hex, $testValue);
		
	}
	
}
