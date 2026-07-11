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

## Architecture

### Five-stage pipeline

```
Source files → Assembler → Specification → Augmenters → Compiler → OpenAPI document
```

1. **Assembler** scans source files, instantiates attributes from reflection, and resolves nesting
2. **Specification** is a flat, typed container holding all collected attributes
3. **Augmenters** enrich the specification with inferred data (types, descriptions, refs, tags) via a grouped pipeline (resolve → reduce → augment)
4. **Compiler** transforms the specification into a versioned OpenAPI document array
5. **Builder** is the unified entry point that orchestrates the pipeline

### Key design decisions

**Slot-map driven nesting.** Each attribute declares its relationships via two methods:
- `merge()` returns `[ParentClass => 'slot']` — how this attribute composes into siblings on the same reflector
- `contains()` returns `[ChildClass => 'slot']` — which child attributes from inner reflectors this attribute absorbs

Slots use `[]` suffix for collection append (`'parameters[]'`), bare name for scalar assignment (`'requestBody'`). This eliminates reflection-based nesting resolution entirely.

**Two-pass assembly.** The Assembler runs two distinct passes:
1. Sibling merge — attributes on the same reflector compose via `merge()` maps
2. Hierarchy resolution — attributes from inner reflectors (method → class, property → class) are absorbed via `contains()` maps

**Immutable-style attributes.** Spec attributes use constructor-promoted public properties. They carry no serialization logic — that responsibility belongs exclusively to compilers.

**Version-aware compilers.** Each OpenAPI version (3.0, 3.1, 3.2) has its own compiler that handles version-specific differences (nullable handling, exclusive min/max, webhooks, etc.) rather than branching inside a single serializer.

**Typed subclasses.** Common patterns get dedicated attribute classes to reduce boilerplate. The base classes (e.g. `OA\Operation`, `OA\Parameter`, `OA\Flow`) can always be used directly but require specifying fields that subclasses pre-fill — for example `#[OA\Operation(path: '/pets', method: 'get')]` vs `#[OA\Operation\Get(path: '/pets')]`.

```
OA\AbstractAttribute
├── OA\OpenApi
├── OA\Info
│   ├── OA\Contact
│   └── OA\License
├── OA\Server
│   └── OA\ServerVariable
├── OA\Tag
├── OA\ExternalDocumentation
├── OA\Operation
│   ├── OA\Operation\Get
│   ├── OA\Operation\Post
│   ├── OA\Operation\Put
│   ├── OA\Operation\Delete
│   ├── OA\Operation\Patch
│   ├── OA\Operation\Head
│   ├── OA\Operation\Options
│   └── OA\Operation\Trace
├── OA\Schema
│   └── OA\Property
├── OA\Parameter
│   ├── OA\Parameter\Path
│   ├── OA\Parameter\Query
│   ├── OA\Parameter\Header
│   └── OA\Parameter\Cookie
├── OA\RequestBody
├── OA\Response
├── OA\Header
├── OA\MediaType
│   └── OA\Encoding
├── OA\Link
├── OA\Example
├── OA\Discriminator
├── OA\Xml
├── OA\Flow
│   ├── OA\Flow\Implicit
│   ├── OA\Flow\Password
│   ├── OA\Flow\ClientCredentials
│   └── OA\Flow\AuthorizationCode
└── OA\Security
    ├── OA\Security\Requirement
    └── OA\Security\Scheme
        ├── OA\Security\Scheme\Http
        ├── OA\Security\Scheme\ApiKey
        ├── OA\Security\Scheme\OAuth2
        ├── OA\Security\Scheme\OpenIdConnect
        └── OA\Security\Scheme\MutualTls
```

### Tri-mode Builder

The `Builder` class supports three modes via `setMode('classic'|'spec'|'hybrid')`:
- **classic** — delegates to the existing `Generator` pipeline
- **spec** — runs the new spec attributes pipeline end-to-end
- **hybrid** — uses the classic `Generator` for scanning/annotation processing, then converts the result via `HybridBridge` into a `Specification` and runs it through the spec augmenters and compilers

## What's done

- All core spec DTOs (OpenApi, Info, Server, Tag, Operation, Schema, Parameter, Response, Header, etc.)
- Assembler with two-pass nesting resolution
- `AttributeFactory` extracted from Assembler for standalone attribute instantiation
- Three compilers (3.0, 3.1, 3.2) with version-specific handling and shared inheritance
- Builder with tri-mode support (classic/spec/hybrid) and CLI integration
- `HybridBridge` converts classic `OpenApi` annotation tree into `Specification` DTOs
- Security namespace (Scheme subclasses + Requirement)
- Typed subclasses for Parameter, Flow, and Operation
- All examples ported to spec attributes (see table below)
- Pipeline classes tested (Assembler, Compilers, Builder)
- Augmenter infrastructure: `PipeInterface` with `@template` generics, Pipeline grouping (resolve → reduce → augment), `Pipeline::get()` for typed configuration
- `SpecificationWalker` — instance-based tree traversal with unified schema descent, callback support, and response header coverage
- All augmenters implemented (see table below)
- Shared `Type\TypeResolver` core producing `SchemaType` value objects — used by both the spec-attributes `Type` augmenter and the classic `TypeInfoTypeResolver`, confirming identical type resolution behavior
- PHPStan clean (all docblock type references use `OA\` alias correctly)
- Rector excludes `tools/` to avoid conflict with cs-fixer FQN shortening

### Augmenter status

| Augmenter | Group | Description | Status |
|---|---|---|---|
| `Type` | resolve | Infers schema type, format, nullable, items, refs from PHP types and docblocks | Done |
| `Ref` | resolve | Resolves FQCN `$ref` values to JSON Reference paths, including discriminator mappings | Done |
| `Docblock` | augment | Summary, description, deprecated from PHPDoc | Done |
| `OperationId` | augment | Generates operationId from reflector context, with `hash` option | Done |
| `Tag` | augment | Auto-generates global tags from operation usage | Done |
| `ExpandHierarchy` | augment | Trait/interface allOf composition, non-schema interface member merging | Done |
| `Enums` | augment | Resolves PHP enum backing types into schema enum/type values | Done |
| `InferNames` | augment | Auto-names component keys from reflector class/context | Done |
| `MediaType` | augment | Re-keys encoding by property name | Done |
| `CleanUnused` | reduce | Removes unreferenced components (iterative, handles nested deps) | Done |
| `PathFilter` | reduce | Filters operations by tag/path regex patterns | Done |

## Example coverage

All 10 example specs now have spec-attribute versions. Most produce identical output to classic (shared yaml fixtures); two have spec-specific fixtures documenting current behavioral gaps.

| Example | Shared fixture | Spec-specific fixture | Notes |
|---|---|---|---|
| api | ✓ | — | Original spec example |
| misc | ✓ | — | Callbacks, security, enums |
| nesting | ✓ | — | Multi-level class inheritance |
| petstore | ✓ | — | Classic CRUD example |
| polymorphism | ✓ | — | oneOf/allOf/discriminator |
| using-interfaces | ✓ | — | Interface-based allOf composition |
| using-links | ✓ | — | Response links |
| using-refs | — | ✓ | See gaps below |
| using-traits | — | ✓ | See gaps below |
| webhooks | ✓ | — | 3.1+ webhooks |

### Documented gaps (spec-specific fixtures)

**using-refs** — Path-level parameters (`PathItem`) are not yet supported in the spec pipeline. The classic pipeline resolves `$ref` on path parameters via its processor chain; in spec mode these must be declared per-operation. The spec fixture documents this difference with a TODO comment.

**using-traits** — Trait property handling differs from classic:
- Trait properties are merged directly into consuming schemas (e.g. Product gets `colour`, `plating`, `whistle` as own properties in addition to the allOf ref)
- BellsAndWhistles includes `bell`/`whistle` from sub-traits directly in its own allOf fragment
- No `example` values on standalone trait schema properties (only on explicitly annotated ones)
- `CustomName-Blink` uses hyphen in schema name (previously used invalid `/` slash)
- Explicit `type` on all property schemas

These gaps represent the current state, not intentional design choices. The trait-merging differences touch on broader extension/sharing features that would be good to consider together. PathItem support may be straightforward to add since it's a first-class spec concept, but both areas are deferred for now. The spec-specific fixtures serve as regression tests and migration reference.

## Classic processor mapping

How each classic processor maps to the new pipeline:

| Classic Processor | Spec-Attributes Equivalent | Stage | Status |
|---|---|---|---|
| ExpandClasses | `ExpandHierarchy` + Assembler (`contains()` maps) | augment + assembly | Done |
| ExpandTraits | `ExpandHierarchy` + Assembler (`contains()` maps) | augment + assembly | Done |
| ExpandInterfaces | `ExpandHierarchy` + Assembler (`contains()` maps) | augment + assembly | Done |
| ExpandEnums | `Enums` | augment | Done |
| MergeIntoOpenApi | Assembler (builds Specification) | assembly | Done |
| MergeIntoComponents | Compiler (groups into components) | compile | Done |
| MergeJsonContent | N/A — attribute eliminated | — | N/A |
| MergeXmlContent | N/A — attribute eliminated | — | N/A |
| BuildPaths | Compiler (groups by path) | compile | Done |
| AugmentSchemas | Split (see below) | mixed | Done |
| AugmentProperties | `Type` (type/format/nullable + property name) | resolve | Done |
| AugmentParameters | `Type` (type/name/required inference) | resolve | Done |
| AugmentItems | `Type` (array items via SchemaType.items) | resolve | Done |
| AugmentRequestBody | `Type` (required inference from nullable) | resolve | Done |
| AugmentRefs | `Ref` (FQCN → JSON reference) | resolve | Done |
| AugmentDiscriminators | `Ref` (discriminator mapping resolution) | resolve | Done |
| AugmentTags | `Tag` (auto-generate tags from operations) | augment | Done |
| AugmentMediaType | `MediaType` (re-key encoding by property name) | augment | Done |
| DocBlockDescriptions | `Docblock` (summary/description/deprecated) | augment | Done |
| OperationId | `OperationId` | augment | Done |
| CleanUnmerged | Assembler (orphan validation in resolveHierarchy) | assembly | Done |
| CleanUnusedComponents | `CleanUnused` (iterative, handles nested deps) | reduce | Done |
| PathFilter | `PathFilter` | reduce | Done |

**AugmentSchemas split:** This processor's responsibilities are distributed across four concerns:
1. Schema naming from class/trait/interface/enum → `InferNames` (done)
2. `type: object` inference when properties present → `Type` (done)
3. Property merging into parent schema → Assembler via `merge()`/`contains()` (done)
4. allOf merge when both properties + allOf exist → Compiler (done)

## What's next

### Hybrid mode hardening

- Dedicated test coverage for `HybridBridge` (currently exercised only indirectly via examples)
- Migrate scratch tests and doc snippet tests to run in spec and/or hybrid modes
- Validate hybrid output matches classic output for the full example suite

### Testing

- Adopt more tests for the spec pipeline: scratch tests, snippet tests, and edge-case scenarios currently only exercised via classic mode
- Ensure augmenter interactions are covered end-to-end (not just unit-level)

### Documentation

- Add docblocks to augmenter pipes describing their configuration options (for generated reference docs)
- Add class-level docblocks to DTOs with polymorphic subclasses (Operation, Parameter, Flow, Security\Scheme) showing common usage of specialized variants

### Integration

- **AttributeEnricher** — extension point for frameworks (e.g. Nelmio) to translate non-OA attributes (like Symfony `#[Assert\*]`) into spec DTOs during assembly
- CompilerExtension support for vendor output (Attachable)
- Attributes inspired by openapi-extras (e.g. polymorphism helpers, additional validation keywords)
- PathItem support (path-level parameters shared across operations)

### Shipping

- Migration guide and dual-tab documentation
- ~~Remaining examples ported~~ — All 10 examples now have spec versions
- ~~All augmenters implemented~~ — Full classic processor parity achieved
- Mark spec attributes as beta during v6 to allow iteration
- Deprecation path for classic annotations in v7

## Consequences

- Users get a cleaner, more discoverable API with IDE autocompletion on typed attributes
- The compilation step is now testable in isolation (pure function: Specification → array)
- Version-specific logic is contained in individual compilers rather than scattered across processors
- The two-pass assembly model is simpler to reason about than the current processor chain
- Augmenters can be developed and tested independently of the core pipeline
