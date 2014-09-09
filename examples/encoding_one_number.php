<?php

/* include hashids lib */
require_once(__DIR__.'/../lib/Hashids/Hashids.php');

/* create the class object */
$hashids = new Hashids\Hashids('this is my salt');

/* encode one number */
$id = $hashids->encode(1337);

/* `$id` is always a string */
var_dump($id);
exit;
