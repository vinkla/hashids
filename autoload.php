<?php

$sep = DIRECTORY_SEPARATOR;
$basedir = __DIR__.$sep;

$file = 'hashids.php-5-3.php';
if (version_compare(PHP_VERSION, '5.4', '>='))
    $file = 'hashids.php';

require_once $basedir.'lib'.$sep.$file;
