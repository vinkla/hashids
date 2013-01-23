<?php

/* including hashids code */
require_once('../lib/hashids.php-5-3.php');

/* creating class object */
$hashids = new Hashids('this is my salt');

/* encrypting several numbers into one hash */
$hash = $hashids->encrypt(1337, 5, 77, 12345678);

/* decrypting that hash */
$numbers = $hashids->decrypt($hash);

/* $numbers is always an array */
var_dump($hash, $numbers);
exit;
