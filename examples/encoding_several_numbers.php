<?php

/* be sure to require `hashids` in your `composer.json` file first */
require_once(__DIR__.'/../vendor/autoload.php');

/* create the class object */
$hashids = new Hashids\Hashids('this is my salt');

/* encode several numbers into one id */
$id = $hashids->encode(45, 434, 1313, 99);
$nid = $hashids->encode(array(45, 434, 1313, 99));

/* `$id` is always a string */
var_dump($id);
var_dump($nid);
var_dump($id === $nid);
exit;
