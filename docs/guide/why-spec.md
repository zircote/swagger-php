# 🧪 Why spec attributes?

The `spec` attributes are a second way to describe an API to swagger-php, alongside the
classic `OpenApi\Attributes`. They are worth the switch for four reasons, in the order they
tend to matter.

## A document built from values, with nothing to scan

Spec attributes are ordinary objects. `Specification::add()` takes them directly, and a
compiler turns the result into a PHP array — no source files, no scan, no `Generator`:

<<< @/snippets/guide/why-spec/value_objects.php

Given `['rate' => 'number', 'name' => 'string']` that produces:

<<< @/snippets/guide/why-spec/value_objects-3.1.0.yaml

This is the mode to reach for when the API surface is computed rather than declared — an
entity registry, a serializer's metadata, a table of fields. The classic annotation objects
cannot be used this way: their properties carry no type declarations and default to an
`UNDEFINED` sentinel, so reading one back means a sentinel check on every field.

`compile()` returning an array is the other half of it. An integration that merges swagger-php
output with something else takes the array; there is no `toJson()` and `json_decode()` round
trip in between.

## Mistakes reported instead of absorbed

`type: 'date'` is not an OpenAPI type; `type: 'string'` with `format: 'date'` is. Write the
first and the classic pipeline rewrites it to the second and says nothing, so the attribute
stays wrong and the next reader copies it. The spec pipeline reports it:

```
Schema has unknown type "date", expecting one of string, number, integer, boolean, array,
object, null in App\Model\Invoice::$issued
```

Running a real codebase through `hybrid` found three of them, all years old.

The same split runs through the pipeline: it resolves and validates in one pass, and says what
it could not make sense of rather than choosing for you.

## Version handling in one place

The output version is a compiler, not a branch. `3.1.0` is the default, with `3.0.0` and
`3.2.0` as targets:

```shell
> ./vendor/bin/openapi --mode spec --version 3.0.0 src
```

Keywords the target version does not have are dropped or warned about there, once, instead of
each attribute asking which version it is being rendered for.

## Less for an integration to reinvent

Component lookup, traversal and the `PathItem` hierarchy are library API — `ComponentIndex`,
`Walker`, `PathItemHierarchy` — reachable from a `Specification`. There is no `Context` to
populate, and no `_context` to fabricate on an object you built yourself.

## What this costs

Spec attributes are marked beta and live in `OpenApi\Spec`. The [roadmap][roadmap] has the
plan through v7 and v8, including the namespace move that follows classic's removal.

Mixing is supported rather than discouraged: [hybrid mode](/guide/modes) reads your existing
`OpenApi\Attributes` through the spec pipeline, so the augmenters and compilers above apply
before any attribute is rewritten.

[roadmap]: https://github.com/zircote/swagger-php/blob/master/ROADMAP.md
