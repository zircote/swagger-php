# Writing documentation

Conventions for the hand-written pages under `docs/`. For which pages are generated and
must not be edited, see [Documentation toolchain](./docs-toolchain.md).

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

## Detail belongs in exactly one place

Orientation may appear in several places; detail may not.

It is fine — often necessary — for two pages to *introduce* the same subject, because they
are read by different people arriving from different directions. What must not happen is
the same specifics being spelled out twice, because the two copies drift and nothing
reveals which one is stale.

The split that works is by depth, not by topic. A page aimed at users says what a thing
does and why it matters, then links onward. The page aimed at contributors carries the
mechanism, the rules, the exact lists. Neither repeats the other's half.

`reference/architecture.md` and `dev/pipeline.md` are the worked example: the reference
page describes the Assembler in a paragraph and links onward, while the internals page owns
slot maps, the resolution algorithm and the root-attribute list. Before this split both
described slot maps, augmenter ordering and the class hierarchy, in different words.

When you find yourself writing something a second time, that is the signal to link instead.

## Reuse the content, do not copy it

When the same text genuinely has to appear in more than one output, extract it and pull it
in. Copying is what rots.

Two mechanisms are already in use here:

- `docs/snippets/preamble_*.md` — markdown fragments the reference generators splice into
  their pages, via `DocGenerator::snippetContent()`. Editing the fragment updates the
  generated page.
- `<<< @/snippets/path.php` — transcludes a real, test-executed source file into a page, so
  a documented example cannot drift from code that runs.

A third, `<!--@include: ./path.md-->`, is supported by Vitepress but not yet used here. It
suits prose shared between hand-written pages.

Prefer extracting a fragment over pasting, and prefer generating over both where the
content is derivable at all — see [the toolchain notes](./docs-toolchain.md) for what is
already generated.

## Write about the subject, not about the documentation

A page describes its subject. Unless the documentation *is* the subject — as on this page,
or the toolchain notes — it does not explain how the docs are produced or why a
cross-reference can be trusted.

No page, of either kind, needs to announce its own audience or justify its existence. The
title and the link that brought the reader here have already done that.

Three real examples, all from one page:

> The Augmenters reference — **generated from the pipeline itself, so it cannot fall out of
> step with the code**

> see Spec pipeline internals, **which is written for people changing the pipeline rather
> than using it**

> **This page stays at the level of "what happens and in what order"**

Each is true, and each is invisible to the only question the reader has, which is how the
thing works. Whether a reference is generated matters to whoever maintains it; the reader
just follows the link.

The usual tell is a clause attached to a link, explaining the link. Let the link text do
that work: "see [Spec pipeline internals]" is complete. Anything after the comma is the
page talking about itself.

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
ADR-001, two had already rotted.

## Do not claim what you have not verified

If a page compares spec mode to classic mode, run both. Hedged comparisons — "classic mode
*may* emit the array form" — are a sign the comparison was reasoned about rather than
tested, and they are impossible for a reader to act on.

Either verify and state it plainly, or document only the behavior you know.

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

## Reviewing documentation changes

A pass to run over a documentation diff — your own or someone else's. Every item below
caught a real defect in this codebase at least once.

Where a check fails, fix the source rather than the symptom: a wrong generated page means
a wrong docblock or generator, not a page to hand-edit.

### Checks you can run

- [ ] **Generated pages untouched.** Run `composer docs:gen` and confirm the pages listed
      in [the toolchain notes](./docs-toolchain.md) are
      unchanged. Scope the check to those files — `docs/reference/` also holds
      hand-written pages, so a directory-wide check reports your own edits.
- [ ] **Every `composer <script>` mentioned exists** in `composer.json`.
- [ ] **Every code reference resolves.** Class names, `Class::method()`, file paths, CLI
      flags, config keys — check each against the source. This catches the most damaging
      class of error, where confident prose describes an API that was renamed or never
      existed.
- [ ] **Command output was captured, not composed.** Any `--help` text, default config
      dump or API listing should come from a real run.
- [ ] **No line-number citations.** `Foo.php:123` rots silently; cite `Class::method()`.
- [ ] **Links resolve.** Relative links point at files that exist; the site build fails on
      dead internal links, so `composer docs:build` covers the rest.
- [ ] **Section anchors verified, or dropped.** A `#heading-slug` link is only safe when
      the heading is plain words — punctuation and dashes make the generated slug
      ambiguous. If you have not confirmed it, link to the page.
- [ ] **No volatile values.** Counts ("six pages", "around 27 test files"), ports,
      versions, anything that shifts without anyone editing the doc. Derive it, describe it
      qualitatively, or — where the tool announces it — say that instead. A dev server that
      prints its own URL should be documented as printing its URL, not as a port number.
- [ ] **Version and requirement claims match** `composer.json`.
- [ ] **No marketing filler** — "production-ready", "seamless", "powerful", "clean",
      "simply", "robust", "comprehensive".
- [ ] **No stock phrase repeated** across the diff. If the same formulation appears twice,
      one of them is padding.

### Checks that need reading

- [ ] **Detail appears exactly once.** Two pages may introduce the same subject; only one
      may carry the specifics. If you are writing something a second time, link instead.
- [ ] **Claims are verified, not reasoned.** Hedges — "may", "should", "is expected to" —
      usually mark a claim nobody tested. Run it, then state it plainly, or cut it.
- [ ] **The page does not contradict itself.** A claim the reader must ignore two
      paragraphs later is worse than no claim.
- [ ] **The level matches the audience.** User-facing pages say what something does and
      link onward; contributor pages carry the mechanism. Neither should hold the other's
      half.
- [ ] **No commentary about the documentation itself**, unless the docs are the page's
      subject — how a page is generated, why a link is trustworthy. Watch for a clause
      hanging off a link that explains the link.
- [ ] **No page announcing its own audience or purpose.** "This page is for…", "these
      notes exist because…". Applies to contributor pages too, not just user-facing ones.
- [ ] **Nothing restates what the page's own structure already shows.** A status table, a
      heading, or a callout already told the reader; a sentence repeating it only rots.
- [ ] **Spelling follows the file it is in** — `docs/` is US, `src/` is British. Neither is
      worth correcting in bulk.
- [ ] **Shared content is reused, not copied** — see [above](#reuse-the-content-do-not-copy-it).

