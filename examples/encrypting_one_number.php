<?php

/* including hashids code */
require_once('../lib/hashids.php-5-3.php');

/* creating class object */
$hashids = new hashids('this is my salt');

/* encrypting one number */
$hash = $hashids->encrypt(1337);

/* $hash is always a string */
var_dump($hash);
exit;
