# Testing

Conventions and helpers for the test suite.

```shell
composer test                            # the whole suite
composer test -- --filter CompilerTest   # a single class
composer performance                     # perf tests, excluded from the default run
```

`composer test` runs tests only. Code style is `composer lint` and static analysis is
`composer analyse` — each command does one thing, matching how CI runs them.

## Prefer data providers

Most behaviour in this library varies along a few axes — OpenAPI version, processing mode,
annotation vs attribute vs spec input. Repeating a test body per combination gets long and
hides which case actually failed.

Use `#[DataProvider]` and let the provider name the case, so a failure reports *which*
combination broke rather than just the assertion. Most of the suite already works this
way; follow the closest existing example rather than inventing a new shape.

Prefer adding a case to an existing provider over copying a whole test method. Prefer
adding new test cases over modifying existing ones — an existing case usually encodes a
regression somebody cared about.

## Shared helpers

`tests/Concerns/` holds the reusable pieces. Look here before writing setup code:

| Trait | Use for |
|---|---|
| `AssemblesSpecification` | `assemble(...$classes)` — build a `Specification` from classes without touching the filesystem |
| `AssertsBuilderResult` | asserting on a `Builder\Result`, including expected warnings and errors |
| `AssertsSchemaStructure` | comparing compiled schema structure (`allOf` refs + property names) against a YAML fixture, independent of property order |
| `CollectsSpecClasses` | enumerating every `OpenApi\Spec` attribute class, for suite-wide invariants |
| `GeneratesTestMatrix` | building version × mode combinations, with exclusions, and discovering fixtures by glob |
| `UsesExamples` | registering a classloader for a `docs/examples` implementation |

`GeneratesTestMatrix` is the one to reach for when a test needs to run across versions and
modes — it handles the cartesian product, the exclusions, and stable test-case naming, so
providers stay short.

`SlotMapConsistencyTest` is a good example of the suite-wide-invariant style: rather than
testing one attribute, it checks a property that must hold across all of them.

## The docs are part of the test suite

Two tests verify documentation against real output. Both will fail if you add documentation
without its counterpart:

**`DocSnippetsTest`** runs every `docs/snippets/*_an.php` and compares against the matching
`-3.1.0.yaml`, across the applicable modes and implementations. Adding a snippet means
adding *all* of:

- `foo_an.php` (annotations), `foo_at.php` (attributes), `foo_spec.php` (spec attributes)
- `foo-3.1.0.yaml` — the expected output

Missing implementations are skipped rather than failing, but a missing or stale
`-3.1.0.yaml` is a failure. The mode/implementation pairing is deliberate: spec snippets run
only in spec mode, and classic mode never runs spec snippets.

**`ExamplesTest`** does the same for `docs/examples/specs/*`, against the per-version
`*-3.0.0.yaml` / `*-3.1.0.yaml` / `*-3.2.0.yaml` fixtures.

This is why changing pipeline output shows up as a wall of documentation failures. That is
the suite working: the expected YAML files are the specification of what the pipeline
produces, and they are what the published docs display.

## Fixtures

- `tests/Fixtures/` — general test fixtures
- `tests/Fixtures/Scratch/` — spec-pipeline scratch fixtures with expected YAML, exercised
  by `ScratchTest`
- `docs/examples/specs/` — full worked examples, doubling as published documentation

`composer redocly` validates the generated example specs against the OpenAPI schema. It
passes, with warnings; known problems are suppressed via `.redocly.lint-ignore.yaml`, so a
*new* failure means something genuinely regressed.
