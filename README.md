
![hashids](http://www.hashids.org.s3.amazonaws.com/public/img/hashids.png "Hashids")

### Full Documentation

A small PHP class to generate YouTube-like hashes from numbers. Read documentation at [http://www.hashids.org/php/](http://www.hashids.org/php/)

[![Build Status](https://secure.travis-ci.org/ivanakimov/hashids.php.png)](http://travis-ci.org/ivanakimov/hashids.php)

### Installation

You can either `require()` the lib yourself, or use [Composer](http://getcomposer.org) (packagist has [hashids/hashids](https://packagist.org/packages/hashids/hashids) package).

In your `composer.json` file use:

``` json
{
    "require": {
        "hashids/hashids": "*"
    }
}
```

And run: `php composer.phar install`. After that you can require the autoloader and use Hashids:

``` php
<?php

require_once 'vendor/autoload.php'
$hashids = new Hashids\Hashids('this is my salt');
```

### Example Usage

```php
<?php

$hashids = new Hashids\Hashids('this is my salt');

$hash = $hashids->encrypt(1, 2, 3);
$numbers = $hashids->decrypt($hash);

var_dump($hash, $numbers);
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
	
### Big Numbers

Each number passed to the constructor **cannot be negative** or **greater than 1 billion** (1,000,000,000). Hashids `encrypt()` function will return an empty string if at least one of the numbers is out of bounds. Be sure to check for that. No exception is thrown.

PHP starts approximating numbers when it does arithmetic on large integers (by converting them to floats). Which is usually not a big issue, but a problem when precise integers are needed.

However, if you have either [GNU Multiple Precision](http://www.php.net/manual/en/book.gmp.php) **--with-gmp**, or [BCMath Arbitrary Precision Mathematics](http://www.php.net/manual/en/book.bc.php) **--enable-bcmath** libraries installed, Hashids will increase its upper limit to `PHP_INT_MAX` which is **int(2147483647)** on 32-bit systems, or **int(9223372036854775807)** on 64-bit.

It will then use regular arithmetic on numbers less than 1 billion (because it's faster), and one of these libraries if greater than. GMP takes precedence over BCMath.

You can get the upper limit by doing: `$hashids->get_max_int_value();` -- which will still be **1 billion** if neither of the libraries is installed.

### Speed

Even though speed is an important factor of every hashing algorithm, primary goal here was encoding several numbers at once while avoiding collisions.

On a *2.7 GHz Intel Core i7 with 16GB of RAM*, it takes roughly:

1. **0.000067 seconds** to encrypt one number.
2. **0.000113 seconds** to decrypt one hash (and ensuring that it's valid).
3. **0.297426 seconds** to generate **10,000** hashes in a `for` loop.

On a *2.26 GHz Intel Core 2 Duo with 8GB of RAM*, it takes roughly:

1. **0.000093 seconds** to encrypt one number.
2. **0.000240 seconds** to decrypt one hash (while ensuring that it's valid).
3. **0.493436 seconds** to generate **10,000** hashes in a `for` loop.

*Sidenote: The numbers tested with were relatively small -- if you increase them, the speed will obviously decrease.*

### Curses! #$%@

This code was written with the intent of placing created hashes in visible places - like the URL. Which makes it unfortunate if generated hashes accidentally formed a bad word.

Therefore, the algorithm tries to avoid generating most common English curse words. This is done by never placing the following letters next to each other:
	
	c, C, s, S, f, F, h, H, u, U, i, I, t, T
	
### Notes

- If you want to squeeze out even more performance, set a shorter alphabet. Hashes will be less random and longer, but calculating them will be faster.
- Since version 0.2.0, speed has been improved so be careful using cache now - it might actually take longer to pull from memory.

### Changelog

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

### Contact

I am on the internets [@IvanAkimov](http://twitter.com/ivanakimov)

### License

MIT License. See the `LICENSE` file. Use it in commercial or open source projects; don't break the Internet.