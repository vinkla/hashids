<?php

require_once('../lib/hashids.php-5-3.php');
$hashids = new hashids('this is my salt', 0, 'abcdefgh123456789'); /* default minimum hash length; custom alphabet */

$hash = $hashids->encrypt(1, 2, 3, 4);
$numbers = $hashids->decrypt($hash);

var_dump($hash, $numbers);
exit;