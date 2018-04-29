[![hashids](http://hashids.org/public/img/hashids.gif "Hashids")](http://hashids.org/)

[![Build Status](https://img.shields.io/travis/ivanakimov/hashids.php/master.svg?style=flat)](https://travis-ci.org/ivanakimov/hashids.php)
[![StyleCI](https://styleci.io/repos/4026744/shield?style=flat)](https://styleci.io/repos/4026744)
[![Coverage Status](https://img.shields.io/codecov/c/github/ivanakimov/hashids.php.svg?style=flat)](https://codecov.io/github/ivanakimov/hashids.php)
[![Total Downloads](https://img.shields.io/packagist/dm/hashids/hashids.svg?style=flat)](https://packagist.org/packages/hashids/hashids)
[![Latest Version](https://img.shields.io/packagist/v/hashids/hashids.svg?style=flat)](https://github.com/ivanakimov/hashids.php/releases)
[![License](https://img.shields.io/packagist/l/hashids/hashids.svg?style=flat)](https://packagist.org/packages/hashids/hashids)

**Hashids** is small PHP library to generate YouTube-like ids from numbers. Use it when you don't want to expose your database ids to the user: [http://hashids.org/php](http://hashids.org/php)

## Getting started

Require this package, with [Composer](https://getcomposer.org), in the root directory of your project.

```bash
$ composer require hashids/hashids
```

Then you can import the class into your application:

```php
use Hashids\Hashids;

$hashids = new Hashids();

$hashids->encode(1);
```

> **Note:** Hashids requires either the [BC Math](https://secure.php.net/manual/en/book.bc.php) or [GMP](https://secure.php.net/manual/en/book.gmp.php) extension in order to work.

## Quick Example

```php
use Hashids\Hashids;

$hashids = new Hashids();

$id = $hashids->encode(1, 2, 3); // o2fXhV
$numbers = $hashids->decode($id); // [1, 2, 3]
```

## More Options

**A few more ways to pass to `encode()`:**

```php
use Hashids\Hashids;

$hashids = new Hashids();

$hashids->encode(1, 2, 3); // o2fXhV
$hashids->encode([1, 2, 3]); // o2fXhV
$hashids->encode('1', '2', '3'); // o2fXhV
$hashids->encode(['1', '2', '3']); // o2fXhV
```

**Make your ids unique:**

Pass a project name to make your ids unique:

```php
use Hashids\Hashids;

$hashids = new Hashids('My Project');
$hashids->encode(1, 2, 3); // Z4UrtW

$hashids = new Hashids('My Other Project');
$hashids->encode(1, 2, 3); // gPUasb
```

**Use padding to make your ids longer:**

Note that ids are only padded to fit **at least** a certain length. It doesn't mean that your ids will be *exactly* that length.

```php
use Hashids\Hashids;

$hashids = new Hashids(); // no padding
$hashids->encode(1); // jR

$hashids = new Hashids('', 10); // pad to length 10
$hashids->encode(1); // VolejRejNm
```

**Pass a custom alphabet:**

```php
use Hashids\Hashids;

$hashids = new Hashids('', 0, 'abcdefghijklmnopqrstuvwxyz'); // all lowercase
$hashids->encode(1, 2, 3); // mdfphx
```

**Encode hex instead of numbers:**

Useful if you want to encode [Mongo](https://www.mongodb.com)'s ObjectIds. Note that *there is no limit* on how large of a hex number you can pass (it does not have to be Mongo's ObjectId).

```php
use Hashids\Hashids;

$hashids = new Hashids();

$id = $hashids->encodeHex('507f1f77bcf86cd799439011'); // y42LW46J9luq3Xq9XMly
$hex = $hashids->decodeHex($id); // 507f1f77bcf86cd799439011
```

## Pitfalls

1. When decoding, output is always an array of numbers (even if you encode only one number):

	```php
	use Hashids\Hashids;

	$hashids = new Hashids();

	$id = $hashids->encode(1);

	$hashids->decode($id); // [1]
	```

2. Encoding negative numbers is not supported.
3. If you pass bogus input to `encode()`, an empty string will be returned:

	```php
	use Hashids\Hashids;

	$hashids = new Hashids();

	$id = $hashids->encode('123a');

	$id === ''; // true
	```

4. Do not use this library as a security tool and do not encode sensitive data. This is **not** an encryption library.

# Randomness

The primary purpose of Hashids is to obfuscate ids. It's not meant or tested to be used as a security or compression tool. Having said that, this algorithm does try to make these ids random and unpredictable:

No repeating patterns showing there are 3 identical numbers in the id:

```php
use Hashids\Hashids;

$hashids = new Hashids();

$hashids->encode(5, 5, 5); // A6t1tQ
```

Same with incremented numbers:

```php
use Hashids\Hashids;

$hashids = new Hashids();

$hashids->encode(1, 2, 3, 4, 5, 6, 7, 8, 9, 10); // wpfLh9iwsqt0uyCEFjHM

$hashids->encode(1); // jR
$hashids->encode(2); // k5
$hashids->encode(3); // l5
$hashids->encode(4); // mO
$hashids->encode(5); // nR
```

## Curses! #$%@

This code was written with the intent of placing created ids in visible places, like the URL. Therefore, the algorithm tries to avoid generating most common English curse words by generating ids that never have the following letters next to each other:

```
c, f, h, i, s, t, u
```

## License

MIT License. See the [LICENSE](LICENSE) file. You can use Hashids in open source projects and commercial products. Don't break the Internet. Kthxbye.
