
Spec attributes are typed PHP 8.1+ attributes in the `OpenApi\Spec` namespace — the foundation of the
spec-attributes pipeline (`--mode spec` or `--mode hybrid`).

They are immutable data containers with no serialization logic. Relationships between attributes are
declared via `contains()` (what children an attribute absorbs) and `merge()` (what parent an attribute
composes into). The [Assembler](/reference/builder.md) resolves nesting, and [Augmenters](/reference/augmenters.md)
enrich the collected specification before compilation.

Typed subclasses (e.g. `Operation\Get`, `Parameter\Path`, `Flow\AuthorizationCode`) pre-fill common
fields to reduce boilerplate — the base class can always be used directly.
