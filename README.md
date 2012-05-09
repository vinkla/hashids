
# hashids

A tiny PHP class to generate YouTube-like hashes from one or many ids.

## Contents

* **README.md** - documentation and examples
* **LICENSE**
* **lib/**
	* **hashids.php** - main `hashids` class for PHP 5.4 and higher
	* **hashids.php-5-3.php** - `hashids` class for PHP 5.3 (using regular array notation)

## What's it for?

Generating **unique hashes** is beneficial when you do not want to expose your database ids in the URL. It's even more helpful when you do not have to look up in the database what record belongs to what hash.

Instead of storing these hashes in the database and selecting by them, you could encode primary ids and select by those - which is faster. Providing a unique `salt` value to the constructor will make your hashes unique also.

Hashes look similar to what YouTube, Bitly, and other popular websites have: `p9`, `pZsCB`, `qKuBQuxc`. They are case-sensitive, include alphanumeric characters and a dash.

You can customize the alphabet from which your hashes are created. Simply pass it to the constructor yourself.

## What's different?

With this class you could encode several ids into one hash. If you have several objects to keep track of, you could use for example `user_id`, `univesity_id` and `class_id` -- passing *all three ids* at the same time and getting back *one hash*.

This is really useful for complex or clustered systems where you need to remember more than one id.

There is no limit to how many ids you can encode into one hash. The more ids you provide and the bigger the numbers, the longer your hash will be.

## Sample Usage

All integers are expected to be positive.

### Encoding:

To encode a single number:

```php
require_once('lib/hashids.php');
$hashids = new hashids('this is my salt');

$hash = $hashids->encode(12345);
```

`$hash` is now going to be:
	
	7OR
	
To encode multiple numbers into one hash:

```php
require_once('lib/hashids.php');
$hashids = new hashids('this is my salt');

$hash = $hashids->encode(683, 94108, 123, 5);
```

`$hash` is now going to be:

	nEfOM6s2oIz

### Decoding:

Hash decoding is done using the same salt value:

```php
require_once('lib/hashids.php');
$hashids = new hashids('this is my salt');

$first_hash = $hashids->decode('7OR');
var_dump($first_hash);

$second_hash = $hashids->decode('nEfOM6s2oIz');
var_dump($second_hash);
```

Output will be:

```php
array(1) {
  [0]=>
  int(12345)
}
array(4) {
  [0]=>
  int(683)
  [1]=>
  int(94108)
  [2]=>
  int(123)
  [3]=>
  int(5)
}
```

## Security

The primary purpose of hashids is to make ids look different. It's not meant or tested to be used as a security algorithm.

Having said that, this class does try to make these hashes un-guessable and unique.

Let's look at the following example:

```php
require_once('lib/hashids.php');
$hashids = new hashids('this is my salt');

$hash = $hashids->encode(5, 5, 5, 5);
```

`$hash` will be:

	jie1ws6
	
You don't see any repeating patterns that might show there's 4 identical numbers in the hash.

Same with incremented numbers:

```php
require_once('lib/hashids.php');
$hashids = new hashids('this is my salt');

$hash = $hashids->encode(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
```

`$hash` will be :
	
	6utsaI616snh0SdFthj
	
## Bonus

Since these hashes are most likely to be used in user-visible places, like the url -- no matter the salt value, they should not make up basic curse words by design, like the f-bomb or "#2".