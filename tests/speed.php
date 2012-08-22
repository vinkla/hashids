<?php

/* test for speed */

$number_of_ints_to_encrypt_at_once = 3;
$start_at = 0;
$end_at = 100;

/* this script will encrypt AND decrypt (when it decrypts it checks that hash is legit) */

require_once('../lib/hashids.php-5-3.php');
$hashids = new hashids();

function microtime_float() {
	list($usec, $sec) = explode(' ', microtime());
	return ((float)$usec + (float)$sec);
}

$total = 0;
$time_start = microtime_float();

for ($i = $start_at; $i <= $end_at; $i++) {
	
	$numbers = array_fill(0, $number_of_ints_to_encrypt_at_once, $i);
	
	$hash = call_user_func_array(array($hashids, 'encrypt'), $numbers);
	$numbers = $hashids->decrypt($hash);
	
	echo $hash.' - '.implode(', ', $numbers)."\n";
	$total++;
	
}

$time_stop = microtime_float();
$total = number_format($total);

echo "\nTotal hashes created: $total.\nTotal time: ".($time_stop - $time_start)."\n";
exit;