<?php

require_once('../lib/hashids.php-5-3.php');
$hashids = new hashids('this is my salt');

var_dump($hashids->encrypt(1337));
exit;