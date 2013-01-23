
![hashids](http://www.hashids.org.s3.amazonaws.com/public/img/hashids.png "Hashids")

### Full Documentation

A small PHP class to generate YouTube-like hashes from numbers. Read more at [http://www.hashids.org/php/](http://www.hashids.org/php/)

### PHP Versions

- Use `lib/hashids.php-5-3.php` for __PHP 5.3.*__
- Use `lib/hashids.php` for __PHP 5.4.*__

### Example Usage

```php
<?php

require_once('lib/hashids.php');
$hashids = new Hashids('this is my salt');

$hash = $hashids->encrypt(1, 2, 3);
$numbers = $hashids->decrypt($hash);

var_dump($hash, $numbers);
```

	string(5) "laUqtq"
	array(3) {
	  [0]=>
	  int(1)
	  [1]=>
	  int(2)
	  [2]=>
	  int(3)
	}
	
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

**0.2.0 -- Current Stable**
	
	Warning: If you are using 0.1.3 or below, updating to this version will change your hashes. So do not update if you are afraid your hashes will change!
	
- Overall approximately **4x** faster
- Consistent shuffle function uses slightly modified version of [Fisher–Yates algorithm](http://en.wikipedia.org/wiki/Fisher%E2%80%93Yates_shuffle#The_modern_algorithm)
- Generate large hash strings faster (more than 1000 chars)
- When using _minimum hash length_ parameter, hash character disorder has been improved
- Basic English curse words will now be avoided even with custom alphabet
- Class name changed from `hashids` to `Hashids`
- New unit tests with [PHPUnit](https://github.com/sebastianbergmann/phpunit/) (requires latest PHP)
- Composer package at packagist: [https://packagist.org/packages/hashids/hashids](https://packagist.org/packages/hashids/hashids)
- _Minor:_ a bit smaller code overall -- more motivation to port to other languages :P

**0.1.3**

	Warning: If you are using 0.1.2 or below, updating to this version will change your hashes.

- Updated default alphabet (thanks to [@speps](https://github.com/speps))
- Constructor removes duplicate characters for default alphabet as well (thanks to [@speps](https://github.com/speps))

**0.1.2**

	Warning: If you are using 0.1.1 or below, updating to this version will change your hashes.

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

Follow me [@IvanAkimov](http://twitter.com/ivanakimov)

### License

MIT License. See the `LICENSE` file. Use it in commercial or open source projects; don't break the Internet.