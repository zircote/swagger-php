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

Pull request descriptions follow the [template](.github/PULL_REQUEST_TEMPLATE.md): a short
**Overview** explaining why the change exists, in plain language — the problem, not the
implementation — followed by a **Changes** list of the key changes, kept high level and
free of code snippets unless one is genuinely unavoidable. Wrap class names, method calls,
file paths and other identifiers in backticks.

A **Changes** entry names what moved, in one line; the diff is what says how. A condition,
a count, a signature or a renamed method's new behaviour is something the reader gets by
opening the diff, and restating it buries the one or two entries that carry the shape of
the change. The Overview holds the reasoning, so an entry needs no *because*.

Keep the description to the changes at hand. History that lives elsewhere — earlier
attempts, abandoned branches, related work in other pull requests — belongs in the issue
or commit trail, not here, unless it has a direct bearing on the change being reviewed.

The prose rules in [Writing documentation](docs/dev/writing-docs.md) apply to descriptions
and commit messages as well as to pages: state a fact once, do not claim what you have not
verified, no marketing filler, no volatile values, no line-number citations.

Commit subjects follow the same `type(Scope): subject` shape as the title, with `type` one
of `feat`, `fix`, `docs`, `test`, `chore` or `refactor`. A commit message body documents
what the diff does; the reasoning belongs in the pull request description.

## Versioning

swagger-php follows [semantic versioning](https://semver.org). What a change earns:

**Patch** — a fix that changes generated output only where the old output was wrong, an
internal refactor, a documentation or CI change. Anything a consumer can take without
reading the release notes.

**Minor** — new public API, a new annotation or attribute, a new field on an existing one,
and **any widening of a `require` constraint**. A dependency change is a minor even when it
only widens: a patch is expected to be a drop-in with no effect on resolution, and widening
lets consumers install combinations that were impossible before.

**Major** — removing or re-signing public API, narrowing a `require` constraint, raising the
PHP floor, or changing generated output that a correct document could have depended on.

**The commit type does not decide this.** #2196 carried no `feat` — its subject is *"Drop
symfony/console 7.4 requirement"*, which reads like a fix — but it is a minor, because
applications on Symfony 6.4 that could not install the library at all now can. Read the
effect on a consumer, not the prefix. The reverse holds too: a `feat` that only adds a test
helper changes nothing for anyone and is not a minor on its own.

**Generated output is the case that needs care**, because most fixes here change it. Correcting
output that was invalid, or that no valid document could have relied on, is a patch — that is
the bug being fixed. Changing output that was already valid is at least a minor, even when the
new output is better, because a consumer's committed spec file or downstream generator will
see a diff it did not ask for.

**Public API is every class under `src/` that a consumer can reach**, which today means all of
them — nothing is marked `@internal`. In practice the classes people build on are `Generator`,
the analysers and processors, the annotations and attributes, and the `Spec` classes; the
`Console` classes exist for `bin/openapi` and are reached through the command line rather than
extended. That is a description of how things are used, not a promise, so when a change touches
a class outside that list, say so in the pull request rather than assuming nobody is on it.

If a release mixes levels, the highest one wins.

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
