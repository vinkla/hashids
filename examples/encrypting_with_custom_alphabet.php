<?php

/* including hashids code */
require_once('../lib/hashids.php-5-3.php');

/* creating class object with custom alphabet */
$hashids = new Hashids('this is my salt', 0, 'abcdefgh123456789');

/* encrypting several numbers into one hash */
$hash = $hashids->encrypt(1, 2, 3, 4);

/* decrypting the same hash */
$numbers = $hashids->decrypt($hash);

/* $numbers is always an array */
var_dump($hash, $numbers);
exit;
