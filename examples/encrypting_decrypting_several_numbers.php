<?php

require_once('../lib/hashids.php-5-3.php');
$hashids = new hashids('this is my salt');

$hash = $hashids->encrypt(1337, 5, 77, 12345678);
$numbers = $hashids->decrypt($hash);

var_dump($hash, $numbers);
exit;