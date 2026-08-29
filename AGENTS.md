# swagger-php

Generates OpenAPI documents from PHP source. Two pipelines: `classic` (docblock annotations
and `OpenApi\Attributes`) and `spec` (`OpenApi\Spec`), with a `hybrid` bridge between them.
[ROADMAP.md](ROADMAP.md) covers where they are heading.

Terminology is defined in [CONTEXT.md](CONTEXT.md). Classic and spec deliberately use
different words for similar things — check there before naming anything.

## Commands

All tooling runs through composer:

- `composer lint` — code style check (cs-fixer + rector, dry run)
- `composer cs` / `composer rector` — apply style fixes
- `composer analyse` — static analysis (phpstan)
- `composer test` — unit tests (add `-- --filter ClassName` for a single class)
- `composer docs:gen` — regenerate the reference docs
- `composer redocly` — validate generated specs against the OpenAPI schema
- `composer docs:dev` — local docs preview; **long-running, never returns**

## Detail, by area

Read the relevant page before working in that part of the tree:

| Page | Covers |
|---|---|
| [docs/dev/pipeline.md](docs/dev/pipeline.md) | spec pipeline internals — slot maps, mutability, augmenter ordering, where new code goes |
| [docs/dev/testing.md](docs/dev/testing.md) | data providers, the `tests/Concerns` helpers, how documentation is test-verified |
| [docs/dev/docs-toolchain.md](docs/dev/docs-toolchain.md) | which documentation is generated, and the CLI's rough edges |
| [docs/dev/writing-docs.md](docs/dev/writing-docs.md) | conventions for hand-written documentation, and a checklist for reviewing doc changes |

## Conventions

- `protected` over `private` for methods and properties, so downstream can subclass
- British spelling in `src/`, US in `docs/` — follow whichever is local to the file
- New pipeline work goes in `src/Spec/`, `src/Augmenter/`, `src/Compiler/`;
  `src/Annotations/` and `src/Attributes/` are classic and closed to new features —
  [ROADMAP.md](ROADMAP.md) has the v7/v8 plan
- Branches are `type/short-description`; commits are `type(Scope): subject`
  (`feat`, `fix`, `docs`, `chore`, `refactor`)

## Before opening a pull request

See [CONTRIBUTING.md](CONTRIBUTING.md) for the full checklist. The short version:
`composer test`, `composer analyse`, and `composer docs:gen` leaving no diff.
