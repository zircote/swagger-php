
Spec attributes are typed PHP attributes in the `OpenApi\Spec` namespace — the foundation of the
spec-attributes pipeline (`--mode spec` or `--mode hybrid`).

They are data containers with no serialization logic; augmenters fill in derived values. Relationships are
declared via `merge()` (what sibling an attribute composes into on the same reflector) and `contained()`
(what parent types can absorb this attribute from inner reflector levels). The [Assembler](/reference/architecture)
resolves nesting, and [Augmenters](/reference/augmenters.md) enrich the collected specification before compilation.

Typed subclasses (e.g. `Operation\Get`, `Parameter\Path`, `Flow\AuthorizationCode`) pre-fill common
fields to reduce boilerplate — the base class can always be used directly.
