<?php

/* include hashids lib */
require_once(__DIR__.'/../lib/Hashids/Hashids.php');

/* create the class object */
$hashids = new Hashids\Hashids('this is my salt');

/* encode several numbers into one id */
$id = $hashids->encode(45, 434, 1313, 99);

/* `$id` is always a string */
var_dump($id);
exit;
