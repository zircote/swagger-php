# Roadmap

## Overview
This is a high level roadmap predominantly for **v6**, **v7** and **v8**.

In particular this is in relation to the introduction of the new _Spec_ attributes and processing pipeline and what may/will happen when.

## Timeline
### v6
`6.5.0` saw the introduction of the new `Spec` system. Right now this is considered not yet production ready.
The remainder of **v6** will be defined by improving and completing the new system.

### v7
This is where things will start to turn:
* Default mode will switch to `hybrid` - this means existing `classic` projects should keep working via the bridge (probably with the exception of `NelmioApiDocBundle`, as that relies on a lot of actual `classic` features), although they will be routed through the new `spec` pipeline via a custom bridge.
* All classic code - annotations + attributes and related pipeline code will be marked `deprecated`
* All code marked `depecated` in **v6** will be removed
* Bridge is marked `deprecated`
* `Builder::setMode()` is marked `deprecated`
* `nikic/php-parser` is raised to `^5.0` — the `^4.19` branch parses no further than
  PHP 8.3 syntax
## v8
`classic` is removed from the codebase, leaving only the spec pipeline. Annotations are no longer supported at all.

**Finding what to remove.** Code carries `@deprecated` from **v7**, which is the primary
marker and the one tooling understands. Documentation, fixture layout and test-matrix
structure cannot carry it — nothing consumes a docblock in a markdown file or a yaml
expectation — so those are marked inline with the mode they exist to serve: `[classic]`,
`[hybrid]`, or `[classic/hybrid]` for what serves both. `grep -rnE '\[(classic|hybrid)'`
lists them, and all of it goes when `classic` does.

The marker names the mode rather than the release on purpose. A thing is `[hybrid]` for as
long as hybrid exists, which stays true whatever happens to this timeline; `[v8: drop]` would
be a claim about a schedule, and would need revisiting if the schedule moved. The two markers
do not overlap with `@deprecated`: if a thing can carry a docblock, it gets `@deprecated` and
not this.
