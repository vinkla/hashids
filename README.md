[![hashids](https://raw.githubusercontent.com/hashids/hashids.github.io/master/public/img/hashids.gif "Hashids")](https://hashids.org/)

[![Build Status](https://badgen.net/github/checks/vinkla/hashids?label=build&icon=github)](https://github.com/vinkla/hashids/actions)
[![Monthly Downloads](https://badgen.net/packagist/dm/hashids/hashids)](https://packagist.org/packages/hashids/hashids/stats)
[![Latest Version](https://badgen.net/packagist/v/hashids/hashids)](https://packagist.org/packages/hashids/hashids)

**Hashids** is a small PHP library to generate YouTube-like ids from numbers. Use it when you don't want to expose your database numeric ids to users: [https://hashids.org/php](https://hashids.org/php)

<a href="https://sqids.org/">
    <img align="right" src="https://github.com/user-attachments/assets/75864e3a-483c-4b6f-9b0b-66f5e3d6e736" alt="Sqids" width="100" height="100">
</a>

> [!NOTE]
> The creator of Hashids has released a new, upgraded version rebranded as **Sqids**. However, Hashids will continue to be maintained and available for future use. For more information, please visit the [Sqids repository](https://github.com/sqids/sqids-php) and learn how it compares to Hashids on the [Sqids website](https://sqids.org/faq#hashids).

## Getting started

Require this package, with [Composer](https://getcomposer.org), in the root directory of your project.

```bash
composer require hashids/hashids
```

Then you can import the class into your application:

```php
use Hashids\Hashids;

$hashids = new Hashids();

$hashids->encode(1);
```

> **Note** Hashids require either [`bcmath`](https://secure.php.net/manual/en/book.bc.php) or [`gmp`](https://secure.php.net/manual/en/book.gmp.php) extension in order to work.

## Quick Example

```php
use Hashids\Hashids;

$hashids = new Hashids();

$id = $hashids->encode(1, 2, 3); // o2fXhV
$numbers = $hashids->decode($id); // [1, 2, 3]
```

## More Options

#### A few more ways to pass input ids to the `encode()` function:

```php
use Hashids\Hashids;

$hashids = new Hashids();

$hashids->encode(1, 2, 3); // o2fXhV
$hashids->encode([1, 2, 3]); // o2fXhV
$hashids->encode('1', '2', '3'); // o2fXhV
$hashids->encode(['1', '2', '3']); // o2fXhV
```

#### Making your output ids unique

Pass a project name to make your output ids unique:

```php
use Hashids\Hashids;

$hashids = new Hashids('My Project');
$hashids->encode(1, 2, 3); // Z4UrtW

$hashids = new Hashids('My Other Project');
$hashids->encode(1, 2, 3); // gPUasb
```

#### Use padding to make your output ids longer

Note that output ids are only padded to fit **at least** a certain length. It doesn't mean that they will be *exactly* that length.

```php
use Hashids\Hashids;

$hashids = new Hashids(); // no padding
$hashids->encode(1); // jR

$hashids = new Hashids('', 10); // pad to length 10
$hashids->encode(1); // VolejRejNm
```

#### Using a custom alphabet

```php
use Hashids\Hashids;

$hashids = new Hashids('', 0, 'abcdefghijklmnopqrstuvwxyz'); // all lowercase
$hashids->encode(1, 2, 3); // mdfphx
```

#### Encode hex instead of numbers

Useful if you want to encode [Mongo](https://www.mongodb.com)'s ObjectIds. Note that *there is no limit* on how large of a hex number you can pass (it does not have to be Mongo's ObjectId).

```php
use Hashids\Hashids;

$hashids = new Hashids();

$id = $hashids->encodeHex('507f1f77bcf86cd799439011'); // y42LW46J9luq3Xq9XMly
$hex = $hashids->decodeHex($id); // 507f1f77bcf86cd799439011
```

## Pitfalls

1. When decoding, output is always an array of numbers (even if you encoded only one number):

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

4. Do not use this library as a security measure. **Do not** encode sensitive data with it. Hashids is **not** an encryption library.

# Randomness

The primary purpose of Hashids is to obfuscate numeric ids. It's **not** meant or tested to be used as a security or compression tool. Having said that, this algorithm does try to make these ids random and unpredictable:

There is no pattern shown when encoding multiple identical numbers (3 shown in the following example):

```php
use Hashids\Hashids;

$hashids = new Hashids();

$hashids->encode(5, 5, 5); // A6t1tQ
```

The same is true when encoding a series of numbers vs. encoding them separately:

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

## Curse words! #$%@

This code was written with the intent of placing the output ids in visible places, like the URL. Therefore, the algorithm tries to avoid generating most common English curse words by generating ids that never have the following letters next to each other:

```
c, f, h, i, s, t, u
```
