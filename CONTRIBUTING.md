# CONTRIBUTING

Contributions are welcome, and are accepted via pull requests. Please review these guidelines before submitting any pull requests.

## Guidelines

- Please follow the [PSR-2 Coding Style Guide](http://www.php-fig.org/psr/psr-2), enforced by [StyleCI](https://styleci.io).
- Ensure that the current tests pass, and if you've added something new, add the tests where relevant.
- Send a coherent commit history, making sure each individual commit in your pull request is meaningful.
- You may need to [rebase](https://git-scm.com/book/en/v2/Git-Branching-Rebasing) to avoid merge conflicts.
- If you are changing the behavior, or the public api, you may need to update the docs.
- Please remember that we follow [SemVer](http://semver.org).

## Running Tests

You will need an install of [Composer](https://getcomposer.org) before continuing.

First, install the dependencies:

```sh
$ composer install
```

Then run [PHPUnit](https://phpunit.de):

```sh
$ vendor/bin/phpunit
```

If the test suite passes on your local machine you should be good to go.

When you make a pull request, the tests will automatically be run again by [Travis CI](https://travis-ci.org).
