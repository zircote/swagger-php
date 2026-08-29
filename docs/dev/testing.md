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

Most behavior in this library varies along a few axes — OpenAPI version, processing mode,
annotation vs attribute vs spec input. Repeating a test body per combination gets long and
hides which case actually failed.

Use `#[DataProvider]` and let the provider name the case, so a failure reports *which*
combination broke rather than just the assertion. Most of the suite already works this
way; follow the closest existing example rather than inventing a new shape.

Prefer adding a case to an existing provider over copying a whole test method. Prefer
adding new test cases over modifying existing ones — an existing case usually encodes a
regression somebody cared about.

### Build objects in the test, not in the provider

PHPUnit evaluates providers while collecting tests, before coverage recording starts, so
anything constructed in a provider is **asserted but never counted as covered**. Yield class
names and arguments; construct in the test body.

The failure is silent — tests pass either way — and only shows up when someone runs coverage
and wonders why a tested class reads as untouched.

## Prefer a fixture to an assertion

A test that asserts what the implementation currently produces captures the status quo. That
is worth having, but it is not evidence the output is *correct* — it only pins today's
behaviour so tomorrow's change is visible.

Where the output is a specification document, a scratch fixture is stronger. It runs the
whole pipeline rather than one stage, it produces a file per OpenAPI version, and those files
are linted by `composer redocly` against the real schema. The expectation then comes from the
specification rather than from whoever wrote the test.

Concretely: the attributes now covered by `Fixtures/Scratch/Auth-spec.php` were first covered
by a unit test asserting compiler output. Every case passed. The fixture that replaced it
immediately failed Redocly, because the pipeline was emitting `type: mutualTLS` into OpenAPI
3.0 documents, where that type does not exist. The assertions had faithfully encoded the bug.

Reach for a unit test when there is no document to validate — `ComponentIndexTest` and
`SlotMapConsistencyTest` are the right shape, because neither has a YAML counterpart.

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
`-3.1.0.yaml` is a failure. The mode/implementation pairing is deliberate: spec mode runs
only spec snippets and classic mode never runs them, but hybrid runs all three.

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
