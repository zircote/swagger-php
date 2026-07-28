# Spec Attributes Pipeline

> Living document — tracks design decisions and progress for the spec attributes pipeline.
> Will become a formal ADR once the feature is complete.

## Context

The classic swagger-php pipeline was designed around annotation objects that are deeply mutable, carry their own serialization logic (`jsonSerialize`), and rely on a complex processor chain for assembly. Over time this has created several pain points:

- Annotations are both data containers and serialization logic, making them hard to reason about
- The processor chain is order-dependent and difficult to extend
- Nesting rules are encoded implicitly via `$_nested` arrays and reflection-heavy logic
- No clear separation between "collecting what the user wrote" and "producing the output document"

The spec attributes pipeline introduces a clean separation of concerns with explicit, typed PHP 8.1+ attributes.

### Additional augmenter pipes

- **`EnumDescription`** — generate human-readable descriptions from PHP enum cases (ported from openapi-extras). Ships in the default pipeline but disabled by default; enable via `$builder->getAugmenters()->get(EnumDescription::class)->setEnabled(true)`.

### Shipping

- Migration guide and dual-tab documentation
- Architecture reference document — comprehensive guide covering the full pipeline, extension points, and design rationale
- Mark spec attributes as beta during v6 to allow iteration
- Deprecation path for classic annotations in v7

### Version timeline

- **v6.x** — spec pipeline ships as opt-in (`setMode(Mode::SPEC)`). Classic remains default. Both modes available side-by-side.
- **v7** — spec mode becomes default. Classic still available via `setMode(Mode::CLASSIC)`. Remove legacy namespaces (`Annotations\*`, `Attributes\*`), `Context`, `Analysis`, doctrine support. Introduce `ProcessorInterface::process(Specification)`.
- **v8** — classic mode removed entirely. Builder is spec-only.

### TODO/evaluate

Re-evaluate support for convenience attributes that reduce boilerplate in common patterns:

~~- **`Items`** — shorthand for array item schema declaration; this probably should be extending `OA\Items` and get a dedicated `PipeInterface` augmenter.~~
~~- **`JsonContent`** / **`XmlContent`** — shorthand for wrapping a schema in a media type with the appropriate content type; a new `AttributeTranslatorInterface` should be implemented to handle the translation of these attributes.~~
- Optional `OA\Property` if `OA\Schema` present and the default property name is used (empty `#[OA\Property])`)
- Adjust attribute parameter types to aid downstream projects?
- A `OA\Schema\Ref` attribute (with title/description 3.1.0+), $ref required attribute - extends `OA\Schema`
- test to verify merges()/contains() consistency (with property type checks)
~~- attachable example/test~~
~~- review [] property types that could also accept a single: type|list<type>~~
~~- enums for fixed strings, like flows->implicit, etc~~
