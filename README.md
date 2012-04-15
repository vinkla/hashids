
# hash-ids

A tiny class to generate YouTube-like hashes from one or many ids.

## Contents

* **README.md** - documentation and examples
* **LICENSE**
* **lib/**
	* **hash_ids.php** - main `hash_ids` class for PHP 5.4 and higher
	* **hash_ids.php-5-3.php** - `hash_ids` class for PHP 5.3 (using regular array notation)

## What's it for?

Generating **unique hashes** is beneficial when you do not want to expose your database ids in the URL. It's even more helpful when you do not have to look up in the database what record belongs to what hash.

Instead of storing these hashes in the database and selecting by them, you could encode primary ids and select by those - which is faster. Providing a unique `salt` value to the constructor will make your hashes unique also.

Hashes look similar to what YouTube, Bitly, and other popular websites have: `p9`, `pZsCB`, `qKuBQuxc`. They are case-sensitive, include alphanumeric characters and a dash.

## What's different?

With this class you could encode several ids into one hash. If you have several objects to keep track of, you could use for example `user_id`, `univesity_id` and `class_id` -- passing *all three ids* at the same time and getting back *one hash*.

There is no limit to how many ids you can encode into one hash. The more ids you provide and the bigger the numbers, the longer your hash will be.

## Sample Usage

All integers are expected to be positive.

### Encoding:

To encode a single number:

	require_once('lib/hash_ids.php');
	$hash_ids = new hash_ids('this is my salt');
	
	$hash = $hash_ids->encode(12345);
	
`$hash` is now going to be:

	7OR

To encode multiple numbers into one hash:

	require_once('lib/hash_ids.php');
	$hash_ids = new hash_ids('this is my salt');
	
	$hash = $hash_ids->encode(683, 94108, 123, 5);
	
`$hash` is now going to be:

	nEFOM6s7wI6
	

### Decoding:

Hash decoding is done using the same salt value:

	require_once('lib/hash_ids.php');
	$hash_ids = new hash_ids('this is my salt');
	
	$first_hash = $hash_ids->decode('7OR');
	var_dump($first_hash);
	
	$second_hash = $hash_ids->decode('nEFOM6s7wI6');
	var_dump($second_hash);
	
Output will be:

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

## Security

The primary purpose of this hash function is to make ids look different. It's not meant or tested to be used primarily as a security algorithm.

Having said that, this class does try to make these hashes un-guessable and unique.

Let's for example look at the following code:

	require_once('lib/hash_ids.php');
	$hash_ids = new hash_ids('this is my salt');
	
	$hash = $hash_ids->encode(5, 5, 5, 5);
	
`$hash` will be:

	jief5sd
	
You don't see any repeating patterns that might show there's 4 identical numbers in the hash.

Same with incremented numbers:

	require_once('lib/hash_ids.php');
	$hash_ids = new hash_ids('this is my salt');
	
	$hash = $hash_ids->encode(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
	
`$hash` will be :

	6uts5Iaf2s7hjSw16ho

## Bonus

Since these hashes are most likely to be used in user-visible places, like the url -- no matter the salt value, they will not make up basic curse words by design, like the f-bomb or "#2".