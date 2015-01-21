
![hashids](http://www.hashids.org.s3.amazonaws.com/public/img/hashids.png "Hashids")

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

Changelog
-------

**1.0.5**:

- bug fix for passing empty array to `encode` (thanks [@bpahan](https://github.com/ivanakimov/hashids.php/issues/32))

**1.0.3** & **1.0.4**:

- adjusting examples (thanks [@Trismegiste](https://github.com/ivanakimov/hashids.php/pull/28))
- proper version bump in `const VERSION`

**1.0.2**

- PSR-2 cleanup + interface changes (thanks [@Trismegiste](https://github.com/ivanakimov/hashids.php/pull/23))
- `encode()` can accept array of integers (thanks [@leunggamciu](https://github.com/ivanakimov/hashids.php/pull/24))

**1.0.1**

- bug fix for `encode_hex()` (thanks [@leihog](https://github.com/ivanakimov/hashids.php/pull/20))
- unit test for `encode_hex()/decode_hex()`

**1.0.0**

- Several public functions are renamed to be more appropriate:
	- Function `encrypt()` changed to `encode()`
	- Function `decrypt()` changed to `decode()`
	- Function `encrypt_hex()` changed to `encode_hex()`
	- Function `decrypt_hex()` changed to `decode_hex()`
	
	Hashids was designed to encode integers, primary ids at most. We've had several requests to encrypt sensitive data with Hashids and this is the wrong algorithm for that. So to encourage more appropriate use, `encrypt/decrypt` is being "downgraded" to `encode/decode`.

- Version tag added: `1.0`
- `README.md` updated

**0.3.1**

- Added *encrypt_hex()* and *decrypt_hex()* support
- Minor: Relaxed integer check in *encrypt()* function (can now pass strings of numbers)

**0.3.0 - Warning: Hashes change in this version:**

- Bug fix: better handling of big numbers: [https://github.com/ivanakimov/hashids.php/issues/3](https://github.com/ivanakimov/hashids.php/issues/3) (thanks [@tobsn](https://github.com/tobsn) and [@miquelfire](https://github.com/miquelfire))
- Bug fix: exception throwing in constructor
- Default maximum number is set to 1 billion: 1,000,000,000. Unless you have [GNU Multiple Precision](http://www.php.net/manual/en/book.gmp.php) or [BCMath Arbitrary Precision Mathematics](http://www.php.net/manual/en/book.bc.php) library installed - then `PHP_INT_MAX` is used.
- Cleanup: private variables use underscores

**0.2.1**

- General directory cleanup + improvements
- Now only one library file for both PHP 5.3 and PHP 5.4
- Constants uppercased
- Namespace `Hashids` added to library class

**0.2.0 - Warning: Hashes change in this version:**
	
- Overall approximately **4x** faster
- Consistent shuffle function uses slightly modified version of [Fisher–Yates algorithm](http://en.wikipedia.org/wiki/Fisher%E2%80%93Yates_shuffle#The_modern_algorithm)
- Generate large hash strings faster (more than 1000 chars)
- When using _minimum hash length_ parameter, hash character disorder has been improved
- Basic English curse words will now be avoided even with custom alphabet
- Class name changed from `hashids` to `Hashids`
- New unit tests with [PHPUnit](https://github.com/sebastianbergmann/phpunit/) (requires latest PHP)
- Composer package at packagist: [https://packagist.org/packages/hashids/hashids](https://packagist.org/packages/hashids/hashids)
- _Minor:_ a bit smaller code overall -- more motivation to port to other languages :P

**0.1.3 - Warning: Hashes change in this version:**

- Updated default alphabet (thanks to [@speps](https://github.com/speps))
- Constructor removes duplicate characters for default alphabet as well (thanks to [@speps](https://github.com/speps))

**0.1.2 - Warning: Hashes change in this version:**

- Minimum hash length can now be specified
- Added more randomness to hashes
- Added unit tests
- Added example files
- Changed warnings that can be thrown
- Renamed `encode/decode` to `encrypt/decrypt`
- Consistent shuffle does not depend on md5 anymore
- Speed improvements

**0.1.1**

- Speed improvements
- Bug fixes

**0.1.0**
	
- First commit

Contact
-------

I am on the internets [@IvanAkimov](http://twitter.com/ivanakimov)

License
-------

MIT License. See the `LICENSE` file. You can use Hashids in open source projects and commercial products. Don't break the Internet. Kthxbye.