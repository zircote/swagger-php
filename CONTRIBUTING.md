## Contributing

Contributions of any kind are welcome.

Feel free to submit [Github Issues](https://github.com/zircote/swagger-php/issues)
or [pull requests](https://github.com/zircote/swagger-php/pulls).


## Quick Guide

The documentation site has [some details](https://zircote.github.io/swagger-php/guide/under-the-hood.html#documentation) about internals.

### How-To

* [Fork](https://help.github.com/articles/fork-a-repo/) the repo.
* [Checkout](https://git-scm.com/docs/git-checkout) the branch you want to make changes on.
    * Typically, this will be `master`. Note that most of the time, `master` represents the next release of swagger-php, so Pull Requests that break backwards compatibility might be postponed.
* Install dependencies: `composer install`.
* Create a new branch, e.g. `feature-foo` or `bugfix-bar`.
* Make changes.
* If you are adding functionality or fixing a bug - add a test!

  Prefer adding new test cases over modifying existing ones.
* Update documentation: `composer docs:gen`.
* Run static analysis using PHPStan/Psalm: `composer analyse`.
* Check if tests pass: `composer test`.
* Fix code style issues: `composer cs`.


## Documentation

The documentation website is build from the [docs](docs/) folder with [vitepress](https://vitepress.vuejs.org).
This process involves converting the existing markdown (`.md`) files into static HTML pages and publishing them.

Some reference content is based on the existing code, so changes to annotations, attributes and processors will require to re-generate those markdown files: `composer docs:gen`.

The actual published content is managed in the [gh-pages](https://github.com/zircote/swagger-php/tree/gh-pages)  branch and driven by a [publish action](https://github.com/zircote/swagger-php/actions/workflows/gh-pages.yml).


## Useful commands

### To run both unit tests and linting execute
```shell
composer test
```

### To run static-analysis execute
```shell
composer analyse
```

### Running unit tests only
```shell
./bin/phpunit
```

### Regenerate reference markup docs
```shell
composer docs:gen
```

### Running linting only
```shell
composer lint
```

### To make `php-cs-fixer` fix linting errors
```shell
composer cs
```

### Run dev server for local development of `gh-pages`
```shell
composer docs:dev
```


## Project's Standards

* [PSR-1: Basic Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
* [PSR-2: Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
* [PSR-4: Autoloading Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR-5: PHPDoc (draft)](https://github.com/phpDocumentor/fig-standards/blob/master/proposed/phpdoc.md)
