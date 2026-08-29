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
* Create a new branch named `type/short-description`, e.g. `feat/encoding-shortcut` or
  `fix/empty-schema-serialization`.
* Make changes.
* If you are adding functionality or fixing a bug - add a test!

  Prefer adding new test cases over modifying existing ones.

### Before opening a pull request

- [ ] Tests pass — `composer test`
- [ ] Static analysis is clean — `composer analyse`
- [ ] Code style is clean — `composer lint` to check, `composer cs` and
      `composer rector` to fix
- [ ] Reference docs regenerated — `composer docs:gen` leaves **no** diff
- [ ] Hand-written docs updated for any behaviour or API change

`composer docs:gen` only rebuilds the generated pages; everything else under `docs/` is
hand-written and will not update itself. See [docs/dev/](docs/dev/) for which is which, and
for the conventions those pages follow.

Pull request titles follow `type(Scope): subject`, e.g. `feat(Spec): add encoding shortcut`.


## Documentation

The documentation website is build from the [docs](docs/) folder with [vitepress](https://vitepress.vuejs.org).
This process involves converting the existing markdown (`.md`) files into static HTML pages and publishing them.

Some reference content is based on the existing code, so changes to annotations, attributes and processors will require to re-generate those markdown files: `composer docs:gen`.

The actual published content is managed in the [gh-pages](https://github.com/zircote/swagger-php/tree/gh-pages)  branch and driven by a [publish action](https://github.com/zircote/swagger-php/actions/workflows/gh-pages.yml).


## Useful commands

### Running the unit tests
```shell
composer test
composer test -- --filter CompilerTest   # a single test class
```

### To run static-analysis execute
```shell
composer analyse
```

### Regenerate reference markup docs
```shell
composer docs:gen
```

### Checking code style
```shell
composer lint
```

### Fixing code style issues
`composer lint` runs both php-cs-fixer and rector in dry-run mode, so a failure can come
from either. Apply the fixes with:
```shell
composer cs       # php-cs-fixer
composer rector   # rector
```

### Validate generated specs with Redocly
```shell
composer redocly
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
