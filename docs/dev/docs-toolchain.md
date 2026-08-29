# Documentation toolchain

How the documentation under `docs/` is produced, and which parts of it you may edit by hand.

This covers the *documentation* toolchain only. For the general development loop — lint,
static analysis, tests, and what to run before a pull request — see
[CONTRIBUTING.md](https://github.com/zircote/swagger-php/blob/master/CONTRIBUTING.md).

## Generated pages — do not edit

Six pages are written by `composer docs:gen`. Editing them directly is wasted work: the next
`docs:gen` overwrites your changes, and so does `composer docs:build`, which runs `docs:gen`
first.

| Page | Built from |
|---|---|
| `reference/annotations.md` | `src/Annotations/` docblocks + `snippets/preamble_annotations.md` |
| `reference/attributes.md` | `src/Attributes/` docblocks + `snippets/preamble_attributes.md` |
| `reference/spec-attributes.md` | `src/Spec/` docblocks + `snippets/preamble_spec-attributes.md` |
| `reference/processors.md` | `src/Processors/` docblocks + prose in `ProcessorGenerator` |
| `reference/augmenters.md` | `src/Augmenter/` docblocks + prose in `AugmenterGenerator` |
| `guide/examples.md` | example sources + the per-example `docs/examples/specs/*/Readme.md` |

To change one of these, change its source and re-run `composer docs:gen`.

Note that some prose lives *inside* the generators rather than in any markdown file — the
`-c` and `-D` explanations in the "Configuration" sections are string literals in
`tools/src/Docs/Reference/{Augmenter,Processor}Generator.php`.

Everything else under `docs/` is hand-written, including `reference/architecture.md`,
`reference/builder.md`, `reference/inheritance.md` and all of `guide/` except
`examples.md`. The **top-level** `docs/examples/Readme.md` is hand-written too; only the
per-example Readmes are pulled into the generated page.

## `docs:gen` is a drift check

The generators are deterministic, and committed output is expected to match its source. So:

```shell
composer docs:gen && git status --porcelain docs/
```

Any output means the committed pages have drifted, and the regenerated version is the
correct one. Worth running before opening a pull request.

Run it with an otherwise-clean tree, or scope the `git status` to the six files above —
`docs/reference/` also holds hand-written pages (`architecture.md`, `builder.md`,
`inheritance.md`, `index.md`), so a directory-wide check reports your own edits as drift.

## Commands

| Command | Notes |
|---|---|
| `composer docs:gen` | regenerate the six pages above |
| `composer docs:build` | runs `docs:gen`, then builds the static site |
| `composer docs:dev` | local preview — **long-running**, prints its URL on startup and does not return |

`composer docs:dev` starts a Vitepress dev server and blocks until interrupted. Do not run
it as a build step or from an automated agent loop.

## What counts as a documented config setting

`reference/augmenters.md` and `reference/processors.md` list the options accepted by
`-c name.option=value`. A setting qualifies when it is **a constructor parameter that is
not object typed**:

- constructor parameters are the public configuration contract, by convention
- object typed parameters — factories, resolvers, the generator — are collaborators, not
  settings, and cannot be expressed as a CLI value

This is implemented once, in `DocGenerator::configurableParameters()`, and mirrors what
`Utils\Pipeline::getConfig()` reports at runtime. The two are aligned by hand rather than
by construction; if you change one, check the other. The check is that
`./bin/openapi --mode spec -D src` lists exactly the settings the reference page documents.

## CLI behavior worth knowing

- `-D` / `--defaults` prints the resolved default config, but still requires the `paths`
  argument: `./bin/openapi --mode spec -D src`, not `./bin/openapi --mode spec -D`.
- `--version` sets the target **OpenAPI** version, not the tool version.
- Help output is standard Symfony Console format. If you need it in a document, capture it
  from `./bin/openapi -h` rather than writing it out — a hand-written approximation has
  drifted from reality before.
- Unknown `-c` keys are reported as warnings in **spec** mode, via
  `Pipeline::configure()`. In **classic** mode they are silently ignored, because
  configuration is routed through `Generator::setConfig()` instead. A typo in a classic
  `-c` key fails invisibly.

## Known rough edge

`AugmenterGenerator` and `ProcessorGenerator` share a lot of near-identical code —
`collectOptions()`, `resolveDefault()`, and their CLI prose blocks. A fix to one usually
needs applying to the other. They have also diverged: `AugmenterGenerator` renders through
the `tools/src/Docs/Sections/` abstraction and emits markdown lists, while
`ProcessorGenerator` renders inline and emits HTML `<span>` markup, which is why the two
reference pages look different.
