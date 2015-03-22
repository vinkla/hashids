<?php

/* be sure to require `hashids` in your `composer.json` file first */
require_once(__DIR__.'/../vendor/autoload.php');

/* create the class object */
$hashids = new Hashids\Hashids('this is my salt');

/* encode several numbers into one id */
$id = $hashids->encode(1337, 5, 77, 12345678);

/* decode that id back */
$numbers = $hashids->decode($id);

/* `$numbers` is always an array */
var_dump($id, $numbers);
exit;
