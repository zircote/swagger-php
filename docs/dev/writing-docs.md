# Writing documentation

Conventions for hand-written prose about this project. For which pages are generated and
must not be edited, see [Documentation toolchain](/dev/docs-toolchain).

## Where these rules apply

Most of what follows is about precision and economy, which do not change with the surface.
They apply to the pages under `docs/`, to docblocks in `src/` — which are spliced into the
generated reference pages, so an imprecise one ships as published documentation — and to
pull request descriptions and commit messages.

Three sections are about pages only, and do not transfer: **Structure**, **Spelling**, and
the generated-page items in the review checklist.

Pull request descriptions carry one extra constraint of their own: they describe **the
change**, and include context only where the change cannot be understood without it. How
the work was found, what else was investigated, and what it might lead to are not part of
the diff. [CONTRIBUTING](https://github.com/zircote/swagger-php/blob/master/CONTRIBUTING.md)
has the template and the title format.

Commit message bodies carry the mirror of it: they document **what the diff does**, not why
it exists. A sentence that starts explaining a motive belongs in the pull request
description instead. Routine verification is not a fact about the diff either — that the
change is tested is assumed, so a body noting it says nothing.

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

A third form is the clause that explains why the fact matters — "which is what makes X
possible", "which matters because…", "which is the point". It carries no fact of its own.
State the fact and stop; where the significance is not visible from the fact, the fact is
the wrong one.

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
content is derivable at all — see [the toolchain notes](/dev/docs-toolchain) for what is
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

## Enumerate closed sets only

A list someone has to remember to update is a list that will be wrong.

Enumerate a set only when it is **closed** — defined somewhere in code, changing only when
that code changes. The generated pages are closed: `tools/docgen.php` names them, so a table
listing them holds until someone edits the generator. "Everything else under `docs/`" is
open-ended; it grows whenever anyone adds a page, so it gets described rather than listed.

The same test applies to a count attached to a list. If the set can change, so can the
number — which is why "six pages" goes along with the enumeration it summarised.

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

The codebase is mixed. `normalise` sits alongside `serialization`; both spellings of
`behavio(u)r` appear in `src/` and in `docs/`. There is no single convention to appeal to.

Match whatever the file you are editing already uses, and leave the rest alone. A bulk
correction would churn far more than it fixes.

## Structure

- Code snippets under `docs/snippets/` are executed by the test suite — see
  [Testing](/dev/testing) for what a new snippet requires.
- The `<codeblock>` component renders annotation / attribute / spec variants as tabs; keep
  the three implementations in sync.
- Internal links use site-absolute paths (`/reference/architecture`), not relative file
  paths, so they survive being rendered at a different depth.

## Reviewing documentation changes

A pass to run over a documentation diff — your own or someone else's — and over a pull
request description or commit message. Every item below caught a real defect in this
codebase at least once.

Where a check fails, fix the source rather than the symptom: a wrong generated page means
a wrong docblock or generator, not a page to hand-edit.

### Checks you can run

- [ ] **Generated pages untouched.** `composer docs:gen` leaves no diff on the pages listed
      in [the toolchain notes](/dev/docs-toolchain).
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

- [ ] **No open-ended set enumerated.** A list is for a set defined in code, which changes
      only when that code changes. Anything that grows when someone adds a file gets
      described instead.
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
- [ ] **No clause explaining the significance of the sentence before it** — "which is what
      makes X possible", "which matters because…". State the fact and stop.
- [ ] **Nothing restates what the page's own structure already shows.** A status table, a
      heading, or a callout already told the reader; a sentence repeating it only rots.
- [ ] **Spelling follows the file it is in** — `docs/` is US, `src/` is British. Neither is
      worth correcting in bulk.
- [ ] **Shared content is reused, not copied** — see [above](#reuse-the-content-do-not-copy-it).

