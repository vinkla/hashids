<?php

/* including hashids code */
require_once(__DIR__.'/../lib/Hashids/Hashids.php');

/* creating class object */
$hashids = new Hashids\Hashids('this is my salt');

/* encrypting several numbers into one hash */
$hash = $hashids->encrypt(45, 434, 1313, 99);

/* $hash is always a string */
var_dump($hash);
exit;
