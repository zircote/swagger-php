# Writing documentation

Conventions for the hand-written pages under `docs/`. For which pages are generated and
must not be edited, see [Documentation toolchain](./docs-toolchain.md).

These rules exist because each was broken at some point, and the result read plausibly
enough that nobody noticed.

## State a fact once

Do not restate something the page's own structure already establishes.

The beta status of the spec pipeline, for example, is carried by the **Status** row in
`guide/modes.md`, by `(beta)` in the section headings, by the `::: warning Beta` callouts,
by the version timeline, and by the 🧪 in the page titles. A sentence saying "this is the
only mode not marked beta" adds nothing and rots independently of the places that do.

The test is not "does this sentence read well" but "does the reader learn anything here
they could not already see". This applies to maturity, version requirements, which
namespace to use, and what a mode does.

Marketing filler and restatement filler are the same defect wearing different clothes.
Replacing "the stable, production-ready mode" with "the only mode not marked beta" is not
an improvement.

## Do not hand-write what can be captured

Command output, default configuration, and API listings should be copied from the real
thing, not composed. A hand-written `openapi -h` block once carried the wrong format, a
wrong `--mode` description, a `--version` default that did not exist, and four missing
options — for as long as nobody ran it.

```shell
./bin/openapi -h            # capture this
./bin/openapi --mode spec -D src
```

## Do not cite line numbers

`Class::method()` survives refactoring; `Foo.php:110` does not. Of three line references in
`docs/adr/001`, two had already rotted.

## Do not claim what you have not verified

If a page compares spec mode to classic mode, run both. Hedged comparisons — "classic mode
*may* emit the array form" — are a sign the comparison was reasoned about rather than
tested, and they are impossible for a reader to act on.

Either verify and state it plainly, or document only the behaviour you know.

## Do not contradict the page you are on

Two examples from the same page:

- attributes described as "immutable" while the augmenter section describes writing to them
- "all modes produce the same output", immediately above a table of differences between
  modes

If a claim needs the reader to ignore the next paragraph, cut the claim.

## Spelling

`docs/` is US-spelled throughout (`behavior`, `serialization`, `organized`) — the OpenAPI
specification text it quotes is US-spelled, and the docs follow. `src/` uses British
spelling for identifiers and prose (`normalise`), except `serializ*`, which is US
everywhere.

Follow whichever convention is local to the file. Neither is worth "correcting" in bulk.

## Structure

- Code snippets under `docs/snippets/` are executed by the test suite — see
  [Testing](./testing.md) for what a new snippet requires.
- The `<codeblock>` component renders annotation / attribute / spec variants as tabs; keep
  the three implementations in sync.
- Internal links use site-absolute paths (`/reference/architecture`), not relative file
  paths, so they survive being rendered at a different depth.
