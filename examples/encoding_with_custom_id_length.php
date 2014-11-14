<?php

/* be sure to require `hashids` in your `composer.json` file first */
require_once(__DIR__.'/../vendor/autoload.php');

/* create the class object with minimum hashid length of 8 */
$hashids = new Hashids\Hashids('this is my salt', 8);

/* encode several numbers into one id (length of id is going to be at least 8) */
$id = $hashids->encode(1337, 5);

/* decode the same id */
$numbers = $hashids->decode($id);

/* `$numbers` is always an array */
var_dump($id, $numbers);
exit;
