
![hashids](http://hashids.org/public/img/hashids-logo-normal.png "Hashids")

======

Full Documentation
-------

A small PHP class to generate YouTube-like ids from numbers. Read documentation at [http://hashids.org/php](http://hashids.org/php)

[![hashids](https://api.travis-ci.org/ivanakimov/hashids.php.svg "Hashids")](https://travis-ci.org/ivanakimov/hashids.php)

Installation
-------

You can install Hashids thru [Composer](http://getcomposer.org) (packagist has [hashids/hashids](https://packagist.org/packages/hashids/hashids) package). In your `composer.json` file use:

``` json
{
    "require": {
        "hashids/hashids": "1.0.5"
    }
}
```

And run: `php composer.phar install`. After that you can require the autoloader and use Hashids:

``` php
<?php

require_once 'vendor/autoload.php'
$hashids = new Hashids\Hashids('this is my salt');
```

Updating from v0.3 to 1.0?
-------

Read the `CHANGELOG` at the bottom of this readme!

Example Usage
-------

The simplest way to use Hashids:

```php
<?php

$hashids = new Hashids\Hashids();

$id = $hashids->encode(1, 2, 3);
$numbers = $hashids->decode($id);

var_dump($id, $numbers);
```
	
	string(5) "laHquq"
	array(3) {
	  [0]=>
	  int(1)
	  [1]=>
	  int(2)
	  [2]=>
	  int(3)
	}
	
And an example with all the custom parameters provided (unique salt value, minimum id length, custom alphabet):

```php
<?php

$hashids = new Hashids\Hashids('this is my salt', 8, 'abcdefghij1234567890');

$id = $hashids->encode(1, 2, 3);
$numbers = $hashids->decode($hash);

var_dump($id, $numbers);
```
	
	string(5) "514cdi42"
	array(3) {
	  [0]=>
	  int(1)
	  [1]=>
	  int(2)
	  [2]=>
	  int(3)
	}
	
Curses! #$%@
-------

This code was written with the intent of placing created ids in visible places - like the URL. Which makes it unfortunate if generated hashes accidentally formed a bad word.

Therefore, the algorithm tries to avoid generating most common English curse words. This is done by never placing the following letters next to each other:
	
	c, C, s, S, f, F, h, H, u, U, i, I, t, T
	
Big Numbers
-------

Each number passed to the constructor **cannot be negative** or **greater than 1 billion by default** (1,000,000,000). Hashids `encode()` function will return an empty string if at least one of the numbers is out of bounds. Be sure to check for that -- no exception is thrown.

PHP starts approximating numbers when it does arithmetic on large integers (by converting them to floats). Which is usually not a big issue, but a problem when precise integers are needed.

However, if you have either [GNU Multiple Precision](http://www.php.net/manual/en/book.gmp.php) **--with-gmp**, or [BCMath Arbitrary Precision Mathematics](http://www.php.net/manual/en/book.bc.php) **--enable-bcmath** libraries installed, Hashids will increase its upper limit to `PHP_INT_MAX` which is **int(2147483647)** on 32-bit systems and **int(9223372036854775807)** on 64-bit.

It will then use regular arithmetic on numbers less than 1 billion (because it's faster), and one of these libraries if greater than. GMP takes precedence over BCMath.

You can get the upper limit by doing: `$hashids->get_max_int_value();` (which will stay at **1 billion** if neither of the libraries is installed).

Speed
-------

Even though speed is an important factor of every hashing algorithm, primary goal here was encoding several numbers at once while avoiding collisions.

On a *2.26 GHz Intel Core 2 Duo with 8GB of RAM*, it takes about:

1. **0.000093 seconds** to encode one number.
2. **0.000240 seconds** to decode one id (while ensuring that it's valid).
3. **0.493436 seconds** to generate **10,000** ids in a `for` loop.

On a *2.7 GHz Intel Core i7 with 16GB of RAM*, it takes roughly:

1. **0.000067 seconds** to encode one number.
2. **0.000113 seconds** to decode one id (and ensuring that it's valid).
3. **0.297426 seconds** to generate **10,000** ids in a `for` loop.

*Sidenote: The numbers tested with were relatively small -- if you increase them, the speed will obviously decrease.*

Notes
-------

- If you want to squeeze out even more performance, set a shorter alphabet. Hashes will be less random and longer, but calculating them will be faster.

Contact
-------

I am on the internets [@IvanAkimov](http://twitter.com/ivanakimov)

License
-------

Hashids is licensed under [The MIT License (MIT)](LICENSE).
