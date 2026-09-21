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
| `AssertsSchemaStructure` | comparing compiled schema structure (`allOf` refs + property names) against a YAML fixture, independent of property order |
| `AssertsSpecEquals` | deep-equality on a whole document — YAML, array or `stdClass` — order-independent for maps |
| `CollectsSpecClasses` | enumerating every `OpenApi\Spec` attribute class, for suite-wide invariants |
| `ExpectsLogEntries` | declaring which diagnostics a build is allowed to emit |
| `GeneratesTestMatrix` | building version × mode combinations, with exclusions, and discovering fixtures by glob |
| `ResolvesDeclaredTypes` | resolving a declared property type through `symfony/type-info`, seeing past `@phpstan-type` aliases |
| `UsesExamples` | registering a classloader for a `docs/examples` implementation |
| `UsesFixtures` | resolving paths under `tests/Fixtures/` |

`GeneratesTestMatrix` is the one to reach for when a test needs to run across versions and
modes — it handles the cartesian product, the exclusions, and stable test-case naming, so
providers stay short.

`ExpectsLogEntries` is strict: an entry matching neither an expectation nor an allowance
fails the test, so a new diagnostic cannot appear unnoticed. Prefer `expectLogEntry()` —
`allowLogEntry()` asserts nothing, and is for diagnostics incidental to what the test is
about.

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

A `Scratch` fixture that provokes a diagnostic declares it in `ScratchTest::scratchTestCases()`,
keyed `{fixture}-{version}` when every mode raises it, or `{fixture}-{version}-{mode}` when
only one does. Both keys apply when both are present, so a mode-specific entry adds to the
shared one rather than replacing it.

### What belongs in a `Scratch` fixture

**The unit is the field family, with the attribute as the default when it is small enough to
hold one.** `HeaderObject` carries every `Header` case; `Schema` has too many fields for that,
so it is split by keyword family — `SchemaKeywords`, `Types`, `Nullable`, `ExclusiveMinMax`,
`PropertyItems`, `NestedAdditionalProperties`. A new case belongs in the existing fixture for
its family. It earns a file of its own only when it needs expected logs the rest of the file
must not inherit, or when it cannot be written in the spec attribute API (see the two
constraints below).

**Multi-case files are the norm, not a compromise.** `SchemaKeywords` is the model: a
file-level comment stating the scheme, then one class per case, each named for its case
(`SchemaKeywordsConditional`, `SchemaKeywordsTuple`, `SchemaKeywordsDependent`). The case
documentation lives in the fixture — the comment, and a `description:` on the attribute where
one reads naturally in the output — not in the file name.

**Name the shape, never the bug.** A fixture is named for what it holds, not for why it was
written: no ticket numbers, no `Repro`, no abbreviations. This matters most for the fixtures
that arrive with a bug fix, which is where the pressure to name them after the bug comes from.

Mechanics that are easy to get wrong:

- The namespace is always `OpenApi\Tests\Fixtures\Scratch`, and `Fixtures\Scratch` in the
  `-spec.php` pair. A fixture never declares a namespace of its own.
- The class is the fixture base name plus a case suffix (`HeaderObjectController`), so the
  file name deliberately does not match the class.
- Which means **`Scratch` fixtures are not autoloadable**: `tests/Fixtures` is
  `exclude-from-classmap`, and `ScratchTest` `require_once`s the path instead. Pointing
  `bin/openapi` at a fixture to see what it produces prints `Skipping unknown
  OpenApi\Tests\Fixtures\Scratch\…` and an empty document. That is the autoloader, not a
  broken fixture — `require` the file from a script if you want to run one by hand.

Two constraints cap how much a single fixture should absorb, and both are worth weighing
before folding a case in:

- **Expected logs are per file.** The `{fixture}-{version}` keying above applies to every case
  in the fixture, so folding a diagnostic-raising case into a clean one makes that diagnostic
  expected file-wide, and nothing ties the warning back to the case that raised it.
  `SchemaKeywords-3.0.0` carries four warnings for seven classes.
- **The `-spec.php` pair is all-or-nothing.** `ScratchTest` skips spec mode entirely when the
  pair is missing, so one case that cannot be expressed in the spec attribute API costs spec
  coverage for every other case in the file.

### What goes away with classic

`src/Annotations/`, the classic and hybrid modes and everything that exists to compare them
are v7-only, and v8 removes the lot. What follows carries the mode marker
[ROADMAP.md](../../ROADMAP.md) defines for the things `@deprecated` cannot reach, so the
removal is a grep rather than an untangling:

- **`[classic]` the `{fixture}.php` / `{fixture}-spec.php` pairing itself.** The unsuffixed
  file is the classic source, written in `OpenApi\Attributes`; the `-spec.php` pair is the same
  document in `OpenApi\Spec`. When classic goes the pair is the only source left, and the
  suffix stops meaning anything.
- **`[classic/hybrid]` the mode axis in `ScratchTest::scratchTestCases()`** — `Mode::CLASSIC`
  and `Mode::HYBRID`, the rule that hybrid is held to the spec expectation where a pair
  exists, and the `-classic` / `-hybrid` suffixes on both expected-log keys and yaml overrides
  (`ThirdPartyAnnotation3.1.0-hybrid.yaml` and its siblings).
- **`[classic]` the `legacy` type resolver and its `*-legacy.yaml` expectations.** The matrix
  already excludes `LegacyTypeResolver` from every non-classic mode, so those files are
  classic-only by construction.
- **`[hybrid]` `HybridBridgeTest` and the fixtures under `tests/Fixtures/HybridBridge/`.**

The bridge is the reason some `Scratch` cases exist at all: `tests/Processors/` is classic-only
by construction, so no test there can catch a hybrid run that fails to replicate a processor.
A case added for that reason — a `Header` whose `content` is a `JsonContent`, an `enum` given
as a class string — still earns its place in v8 as a plain spec case, but the *motivation*
recorded in its comment stops applying.

`composer redocly` validates the generated example specs against the OpenAPI schema. It
passes, with warnings; known problems are suppressed via `.redocly.lint-ignore.yaml`, so a
*new* failure means something genuinely regressed.
