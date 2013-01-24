<?php

/* including hashids code */
require_once(__DIR__.'/../lib/Hashids/Hashids.php');

/* creating class object */
$hashids = new Hashids\Hashids('this is my salt');

/* encrypting one number */
$hash = $hashids->encrypt(1337);

/* $hash is always a string */
var_dump($hash);
exit;
