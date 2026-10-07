# 🧪 Why spec attributes?

The `spec` attributes are a second way to describe an API to swagger-php, alongside the
classic `OpenApi\Attributes`. There are four reasons to switch, in the order they tend to
matter.

## A document built from values, with nothing to scan

Spec attributes are ordinary objects. `Specification::add()` takes them directly, and a
compiler turns the result into a PHP array. There are no source files, no scan and no
`Generator`:

<<< @/snippets/guide/why-spec/value_objects.php

Given `['rate' => 'number', 'name' => 'string']` that produces:

<<< @/snippets/guide/why-spec/value_objects-3.1.0.yaml

This suits an API surface that is computed rather than declared, such as an entity
registry, a serializer's metadata or a table of fields. The classic annotation objects
cannot be used this way. Their properties carry no type declarations and default to an
`UNDEFINED` sentinel, so reading one back means a sentinel check on every field.

`compile()` also returns an array. An integration that merges swagger-php output with
something else takes the array, with no `toJson()` and `json_decode()` round trip in
between.

## Mistakes reported instead of absorbed

`type: 'date'` is not an OpenAPI type. `type: 'string'` with `format: 'date'` is. The
classic pipeline rewrites the first into the second without saying anything, so the
attribute stays wrong and the next reader copies it. The spec pipeline reports it:

```
Schema has unknown type "date", expecting one of string, number, integer, boolean, array,
object, null in App\Model\Invoice::$issued
```

Running a real codebase through `hybrid` found three of them, all years old.

The rest of the pipeline works the same way. It resolves and validates in one pass, and
reports what it could not make sense of instead of guessing.

## Version handling in one place

The output version is a compiler, not a branch. `3.1.0` is the default, with `3.0.0` and
`3.2.0` as targets:

```shell
> ./vendor/bin/openapi --mode spec --version 3.0.0 src
```

Keywords the target version does not have are dropped or warned about in the compiler, once.
No attribute has to ask which version it is being rendered for.

## Less for an integration to reinvent

Component lookup, traversal and the `PathItem` hierarchy are library API, reachable from a
`Specification`: `ComponentIndex`, `Walker` and `PathItemHierarchy`. There is no `Context`
to populate, and no `_context` to fabricate on an object built by hand.

## What this costs

Spec attributes are marked beta and live in `OpenApi\Spec`. The [roadmap][roadmap] has the
plan through v7 and v8, including the namespace move that follows classic's removal.

Mixing is supported. [Hybrid mode](/guide/modes) reads existing `OpenApi\Attributes` through
the spec pipeline, so the augmenters and compilers above apply before any attribute is
rewritten.

[roadmap]: https://github.com/zircote/swagger-php/blob/master/ROADMAP.md
