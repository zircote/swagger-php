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

### Four-stage pipeline

```
Source files → Assembler → Specification → Compiler → OpenAPI document
```

1. **Assembler** scans source files, instantiates attributes from reflection, and resolves nesting
2. **Specification** is a flat, typed container holding all collected attributes
3. **Compiler** transforms the specification into a versioned OpenAPI document array
4. **Builder** is the unified entry point that orchestrates the pipeline

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

### Dual-mode Builder

The `Builder` class supports both pipelines via `setMode('classic'|'spec')`. In spec mode it runs the new pipeline; in classic mode it delegates to the existing `Generator`. A future hybrid mode will allow mixing both attribute styles in the same project, enabling incremental migration without a big-bang rewrite.

## What's done

- All core spec DTOs (OpenApi, Info, Server, Tag, Operation, Schema, Parameter, Response, Header, etc.)
- Assembler with two-pass nesting resolution
- Three compilers (3.0, 3.1, 3.2) with version-specific handling
- Builder with dual-mode support and CLI integration
- Security namespace (Scheme subclasses + Requirement)
- Typed subclasses for Parameter, Flow, and Operation
- One working example (`api`) using spec attributes
- Pipeline classes tested (Assembler, Compilers, Builder)
- Augmenter infrastructure: `AugmenterInterface`, Pipeline integration, typed Builder configuration
- `OperationId` augmenter (generates operationId from reflector context, with `hash` option)
- PHPStan clean (all docblock type references use `OA\` alias correctly)
- Rector excludes `tools/` to avoid conflict with cs-fixer FQN shortening

## What's next

### Augmenters (critical path)

The infrastructure is in place (`AugmenterInterface` + Pipeline + Builder config pattern). Remaining augmenters to implement:

- **TypeResolver** — infer type/format/$ref from PHP type hints
- **DocblockReader** — fill summary/description from PHPDoc comments
- **Inheritance** — build allOf/property merging from class hierarchies
- **EnumValues** — populate enum from PHP backed enum cases

### Specification helpers (needed by augmenters)

- Query methods (`find()`, `filter()`, `resolveRef()`) for looking up related attributes

### Integration

- **AttributeEnricher** — extension point for frameworks (e.g. Nelmio) to translate non-OA attributes (like Symfony `#[Assert\*]`) into spec DTOs during assembly
- CompilerExtension support for vendor output (Attachable)
- Attributes inspired by openapi-extras (e.g. polymorphism helpers, additional validation keywords)

### Shipping

- Migration guide and dual-tab documentation
- Remaining examples ported (petstore, polymorphism, webhooks, etc.)
- Mark spec attributes as beta during v6 to allow iteration
- Deprecation path for classic annotations in v7

## Consequences

- Users get a cleaner, more discoverable API with IDE autocompletion on typed attributes
- The compilation step is now testable in isolation (pure function: Specification → array)
- Version-specific logic is contained in individual compilers rather than scattered across processors
- The two-pass assembly model is simpler to reason about than the current processor chain
- Augmenters can be developed and tested independently of the core pipeline
