# Roadmap

## Overview
This is a high level roadmap for **v6**, **v7** and **v8** - what happens to the classic pipeline
and the new _Spec_ attributes and processing pipeline, and when.

The discussion and the tick list of what has shipped are in
[#1953](https://github.com/zircote/swagger-php/issues/1953). This file is the plan; the issue
tracks it.

## Deprecation rule
A `@deprecated` marker names the version that removes the thing:

```php
/** @deprecated since 6.11, removed in 8.0 - use ... instead */
```

Nothing is removed before the version its marker names, and nothing is removed without a marker.
Something marked for `8.0` keeps working through **v7** unchanged. That is what "no cliff" means
here: the marker is the promise.

## Timeline
### v6
`6.5.0` introduced the `Spec` system as opt-in beta. The remainder of **v6** is about completing
it and getting people onto it:

* `Spec` attributes declared frozen, and the **beta** label dropped in that minor
* a rector rule set for `OpenApi\Attributes` -> `OpenApi\Spec`
* a seam to contribute pre-built attributes to a `Specification` before resolution, for
  framework integrations that do not scan
* docblock annotation support marked `@deprecated`, removed in `8.0` - the README has said so
  since 4.8, the code now agrees, and parsing a docblock annotation triggers a runtime
  deprecation once per run
* the relocation shims already marked (`Pipeline`, `SourceFinder`, `Analysers\TokenScanner`,
  `Utils\TypeMapper`, `LegacyTypeResolver`) are removed in `7.0`
* `Generator::UNDEFINED` and `Generator::isDefault()` stay until `8.0` - every classic custom
  processor uses them, so they go with classic and not before

Classic attributes, the `Generator` and the processors are **not** marked in v6. The replacement
is still beta for part of the major, and a stable API is not deprecated in favour of a beta one.

### v7
This is where things turn:
* default mode switches to `hybrid` - existing `classic` projects keep working, routed through
  the new pipeline via the bridge (probably with the exception of `NelmioApiDocBundle`, as that
  relies on a lot of actual `classic` features)
* all classic code - annotations, attributes and the related pipeline code - is marked
  `@deprecated`, removed in `8.0`
* the bridge and `Builder::setMode()` are marked `@deprecated`, removed in `8.0`
* everything whose marker says `7.0` is removed
* `nikic/php-parser` is raised to `^5.0` - the `^4.19` branch parses no further than
  PHP 8.3 syntax
* the v7 migration guide states the v8 namespace move below, so nobody meets it as a surprise

Open: whether the default **output** version follows the default mode. `classic` defaults to
OpenAPI 3.0.0 and `hybrid` to 3.1.0 today, so switching the default mode also switches the
default output version for everyone unless v7 pins 3.0.0. Decided in #1953 before v7.

### v8
`classic` is removed from the codebase, leaving only the spec pipeline. Annotations are no longer
supported at all.

The spec attributes move from `OpenApi\Spec` to `OpenApi\Attributes`. With one pipeline left,
"Spec" is a mode name that means nothing, and `OpenApi\Attributes` is the better home. For code
written against `OpenApi\Spec` that is one `use` line per file, and the rector set does it.

Open: whether `OpenApi\Spec` stays as deprecated aliases for one major, so the move is optional
until v9.

## Finding what to remove
Code carries `@deprecated` with the version that removes it, which is the primary marker and
the one tooling understands. Documentation, fixture layout and test-matrix structure cannot
carry it - nothing consumes a docblock in a markdown file or a yaml expectation - so those are
marked inline with the mode they exist to serve: `[classic]`, `[hybrid]`, or `[classic/hybrid]`
for what serves both. `grep -rnE '\[(classic|hybrid)'` lists them, and all of it goes when
`classic` does.

The marker names the mode rather than the release on purpose. A thing is `[hybrid]` for as
long as hybrid exists, which stays true whatever happens to this timeline; `[v8: drop]` would
be a claim about a schedule, and would need revisiting if the schedule moved. The two markers
do not overlap with `@deprecated`: if a thing can carry a docblock, it gets `@deprecated` and
not this.
