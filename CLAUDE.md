# Tools

All tools are run via composer:

- `composer lint` — check all code style issues (cs-fixer + rector)
- `composer cs` / `composer rector` — fix code style issues
- `composer analyse` — static analysis (phpstan)
- `composer test` — unit tests (phpunit); use `./bin/phpunit` directly for filtered runs (e.g. `./bin/phpunit --filter ClassName`)
- `composer redocly` — validate spec fixtures (currently failing, fixtures not yet valid)
