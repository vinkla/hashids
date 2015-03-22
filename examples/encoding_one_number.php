<?php

/* be sure to require `hashids` in your `composer.json` file first */
require_once(__DIR__.'/../vendor/autoload.php');

/* create the class object */
$hashids = new Hashids\Hashids('this is my salt');

/* encode one number */
$id = $hashids->encode(1337);

/* `$id` is always a string */
var_dump($id);
exit;
