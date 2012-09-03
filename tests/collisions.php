<?php

/* test for collisions with 3 integers */

$start_at = 0;
$end_at = 15;

/* this script will create hashes and check against each other to make sure there are no collisions */

require_once('../lib/hashids.php-5-3.php');
$hashids = new hashids('this is my salt');

$hash_array = array();

$total = 0;

for ($i = $start_at; $i <= $end_at; $i++) {
	for ($j = $start_at; $j <= $end_at; $j++) {
		for ($k = $start_at; $k <= $end_at; $k++) {
			
			$hash = $hashids->encrypt($i, $j, $k);
			echo "$hash - $i, $j, $k\n";
			
			if (!isset($hash_array[$hash]))
				$hash_array[$hash] = "$i, $j, $k";
			else {
				echo "Collision for $hash: $i, $j, $k and ".$hash_array[$hash];
				exit;
			}
			
			$total++;
			
		}
	}
}

$total = number_format($total);
echo "\nNo collisions, ran through $total hashes.\n";

exit;