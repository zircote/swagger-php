# Tools

All tools are run via composer:

- `composer lint` — check all code style issues (cs-fixer + rector)
- `composer cs` / `composer rector` — fix code style issues
- `composer analyse` — static analysis (phpstan)
- `composer test` — lint + unit tests; use `./bin/phpunit` directly to run only tests (e.g. `./bin/phpunit --filter ClassName`)
- `composer redocly` — validate spec fixtures (currently failing, fixtures not yet valid)

# Tests

- Prefer compact tests and data providers over repetition
- Use shared traits from `tests/Concerns/` for common helpers (e.g. `AssemblesSpecification`)
