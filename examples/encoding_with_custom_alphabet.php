<?php

/* be sure to require `hashids` in your `composer.json` file first */
require_once(__DIR__.'/../vendor/autoload.php');

/* create the class object with custom alphabet */
$hashids = new Hashids\Hashids('this is my salt', 0, 'abcdefgh123456789');

/* encode several numbers into one id */
$id = $hashids->encode(1, 2, 3, 4);

/* decode the same id */
$numbers = $hashids->decode($id);

/* `$numbers` is always an array */
var_dump($id, $numbers);
exit;
