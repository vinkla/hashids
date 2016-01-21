# CHANGELOG

**1.0.6**:

- `CHANGELOG` moved to its own file (thanks [@vinkla](https://github.com/ivanakimov/hashids.php/pull/41))
- `.gitattributes` added (thanks [@vinkla](https://github.com/ivanakimov/hashids.php/pull/40))
- autoload with PSR-4 (thanks [@vinkla and @GrahamCampbell](https://github.com/ivanakimov/hashids.php/pull/43))
- `LICENSE` moved to its own file (thanks [@vinkla](https://github.com/ivanakimov/hashids.php/pull/44))
- `.gitignore` file simplified (thanks [@vinkla](https://github.com/ivanakimov/hashids.php/pull/45))
- random `README` cleanups (thanks [@vinkla](https://github.com/ivanakimov/hashids.php/pull/46))
- applied PSR-1 and PSR-2 to the code (thanks [@pablofmorales](https://github.com/ivanakimov/hashids.php/pull/51))
- typo in custom parameters example (thanks [@McMillanThomas](https://github.com/ivanakimov/hashids.php/pull/52))
- testing against PHP 7.0 now (thanks [@vinkla](https://github.com/ivanakimov/hashids.php/pull/54))

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
