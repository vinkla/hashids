<?php

require_once('../lib/hashids.php-5-3.php');
$hashids = new hashids('this is my salt', 30); /* using custom minimum hash length of 30 here */

$hash = $hashids->encrypt(1337, 5, 77, 12345678);
$numbers = $hashids->decrypt($hash);

var_dump($hash, $numbers);
exit;