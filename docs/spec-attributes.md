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

After both passes, only root attributes remain and are added to the `Specification`.

**`isRoot()` as validation, not routing.** The `merge()` and `contains()` maps drive all nesting decisions — `isRoot()` does not control routing. Its role is purely a post-resolution assertion: after the two-pass assembly, every remaining attribute must return `isRoot() === true` or the assembler throws an error. This catches mis-placed attributes early (e.g. a Parameter on a class without a sibling PathItem or Operation).

Most DTOs return a constant (`true` for Schema, Operation, PathItem; `false` for MediaType, Example). Conditional root-ness is used where the same class can be either a component or a nested child — for example `Parameter::isRoot()` returns `true` only when `$this->parameter !== null` (has a component key).

Custom DTOs should declare `merge()` targets to specify where they nest, and `isRoot()` to indicate whether they can land in a `Specification` bucket independently.

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
├── OA\PathItem
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

### Reflectors as relationship glue

Every root DTO carries its originating reflector (`ReflectionClass`, `ReflectionMethod`, etc.). This is the fundamental mechanism for resolving cross-bucket relationships at augmentation time — the assembler is intentionally "dumb" (just collects into buckets), and augmenters use reflectors to reconnect what belongs together.

Key applications:
- **PathItem ↔ Operation binding** — PathItem is placed on a class; operations on methods of that class. The augmenter walks `ReflectionMethod::getDeclaringClass()` to find which PathItem governs an operation.
- **Prefix composition via inheritance** — PathItems on parent classes contribute prefixes. The augmenter walks `ReflectionClass::getParentClass()` to compose the full path prefix chain.
- **OperationId generation** — the reflector provides class/method name context for auto-generated identifiers.

This design keeps the assembler simple and makes cross-cutting relationships resolvable without coupling DTOs to each other.

### Tri-mode Builder

The `Builder` class supports three modes via `setMode('classic'|'spec'|'hybrid')`:
- **classic** — delegates to the existing `Generator` pipeline
- **spec** — runs the new spec attributes pipeline end-to-end
- **hybrid** — uses the classic `Generator` for scanning only (MergeJsonContent/MergeXmlContent), then iterates the `Analysis` annotations directly via `HybridBridge` into a `Specification` and runs it through the full spec augmenter chain and compilers

## What's done

- All core spec DTOs (OpenApi, Info, Server, Tag, Operation, Schema, Parameter, Response, Header, PathItem, etc.)
- Assembler with two-pass nesting resolution
- `AttributeFactory` extracted from Assembler for standalone attribute instantiation
- Three compilers (3.0, 3.1, 3.2) with version-specific handling and shared inheritance
- Builder with tri-mode support (classic/spec/hybrid) and CLI integration
- `HybridBridge` iterates `Analysis` annotations directly into `Specification` DTOs (no tree assembly needed)
- Security namespace (Scheme subclasses + Requirement)
- Typed subclasses for Parameter, Flow, and Operation
- All examples ported to spec attributes (see table below)
- Pipeline classes tested (Assembler, Compilers, Builder)
- Augmenter infrastructure: `PipeInterface` with `@template` generics, Pipeline grouping (resolve → reduce → augment), `Pipeline::get()` for typed configuration
- `SpecificationWalker` — instance-based tree traversal with unified schema descent
- All augmenters implemented (see table below)
- Shared `Type\TypeResolver` core producing `SchemaType` value objects — used by both the spec-attributes `Type` augmenter and the classic `TypeInfoTypeResolver`, confirming identical type resolution behavior

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
| `PathItemResolve` | resolve | Prefix composition, clone-down of tags/security/responses, path inference | Done |

## Example coverage

All 10 example specs now have spec-attribute versions. Most produce identical output across classic, spec, and hybrid modes (shared yaml fixtures). Hybrid mode is tested for all examples and passes except using-refs (PathItem gap).

| Example | Shared fixture | Spec-specific fixture | Notes |
|---|---|---|---|
| api | ✓ | — | Original spec example |
| misc | ✓ | — | Callbacks, security, enums |
| nesting | ✓ | — | Multi-level class inheritance |
| petstore | ✓ | — | Classic CRUD example |
| polymorphism | ✓ | — | oneOf/allOf/discriminator |
| using-interfaces | ✓ | — | Interface-based allOf composition |
| using-links | ✓ | — | Response links |
| using-refs | — | ✓ | PathItem works; hybrid excluded due to ref-path difference |
| using-traits | — | ✓ | Bug fix: explicit type inference |
| webhooks | ✓ | — | 3.1+ webhooks |

### Spec-specific fixture notes

**using-refs** — PathItem spec support is implemented (path-level parameters emitted correctly). The example is excluded from hybrid tests due to an unrelated ref-path difference: classic emits `Product/allOf/1/properties/id` while hybrid emits `Product/properties/id` (hybrid puts properties directly rather than in an allOf wrapper).

**using-traits** — Not a gap; intentional improvement. The spec/hybrid pipeline infers explicit types from PHP declarations more thoroughly than classic.

### Behavioral differences from classic

Intentional improvements or corrections in the spec/hybrid pipeline that produce different output from classic:

| Difference | Classic | Spec/Hybrid | Rationale |
|---|---|---|---|
| Empty `tags` | Always emits `tags: []` | Omits when empty | Per OpenAPI spec, optional fields should be omitted when empty |
| Empty flow `scopes` | Emits `scopes: {}` | Was omitting empty scopes | Bug fix in compiler — scopes is required per OpenAPI spec |
| Type inference on traits | Types often omitted | Explicit types from PHP declarations | Bug fix — classic failed to resolve types on trait properties |
| Docblock on parameters | Only from `@param` on ReflectionParameter | Also matches by parameter name | More thorough — resolves descriptions for non-reflection parameters |
| Promoted property descriptions | Resolved via constructor parameter context | Resolved via ReflectionProperty fallback | Different mechanism, same result |

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

## PathItem design

PathItem is both an OpenAPI spec concept (path-level parameters, summary, description, servers) and a controller-grouping mechanism (prefix composition, shared tags/security). The DTO unifies both roles.

### DTO: `OA\PathItem`

Class-level only. No `path` property — path is always inferred from the operations the PathItem governs.

| Property | OpenAPI output | Controller role |
|---|---|---|
| `prefix` | — (resolved away) | Composable path prefix, inherited via class hierarchy |
| `parameters[]` | Path-level parameters | Shared parameters for all operations |
| `summary` | Path-level summary | — |
| `description` | Path-level description | — |
| `servers[]` | Path-level servers | — |
| `tags[]` | — (cloned to operations) | Shared tags for all operations |
| `security[]` | — (cloned to operations) | Shared security for all operations |
| `responses[]` | — (cloned to operations) | Shared responses for all operations |

### Augmenter: `PathItemResolve` (resolve group, early)

1. **Index** — map each PathItem to its declaring class via reflector
2. **Compose prefixes** — walk `ReflectionClass::getParentClass()` chain, collect ancestor PathItems, compose full prefix
3. **Resolve operation paths** — for each operation, find declaring class, walk up to find governing PathItem, prepend composed prefix to operation path
4. **Clone metadata** — push PathItem tags/security/responses down to operations that don't already declare them
5. **Emit path-level entries** — for each unique resolved path with PathItem parameters/summary/description/servers, set `pathItem->path` (derived) and keep in bucket. Remove prefix-only PathItems with no path-level output.
6. **Warning** — log when a PathItem has path-level properties but no operations match

### Compiler integration

The compiler emits PathItem properties (parameters, summary, description, servers) at path level in the `paths` output, alongside the operation entries grouped by method.

### Example

```php
#[OA\PathItem(prefix: '/api/v1')]
class BaseController {}

#[OA\PathItem(prefix: '/users', tags: ['Users'], parameters: [
    new OA\Parameter\Path(name: 'tenant', schema: new OA\Schema(type: 'string')),
])]
class UserController extends BaseController {
    #[OA\Operation\Get(path: '/list')]
    public function list() {}

    #[OA\Operation\Get(path: '/{id}')]
    public function get($id) {}
}
```

Output paths:
- `/api/v1/users/list` — get operation, tags: ['Users'], path-level parameter: tenant
- `/api/v1/users/{id}` — get operation, tags: ['Users'], path-level parameter: tenant

## What's next

### Hybrid mode hardening

- ~~Validate hybrid output matches classic output for the full example suite~~ — done (all pass except using-refs ref-path difference)
- Dedicated test coverage for `HybridBridge` (currently exercised only indirectly via examples)
- Migrate scratch tests and doc snippet tests to run in spec and/or hybrid modes
- ~~PathItem spec support (path-level parameters, summary, description, servers)~~ — done

### PathItem controller features

- ~~`PathItemResolve` augmenter — prefix composition via class hierarchy, clone-down of tags/security/responses to operations, path inference from contained operations~~ — done

### Testing

- Adopt more tests for the spec pipeline: scratch tests, snippet tests, and edge-case scenarios currently only exercised via classic mode
- Ensure augmenter interactions are covered end-to-end (not just unit-level)
- Augmenter tests should persist expected YAML fixtures (or validate generated specs with redocly as a separate pass)

### Refactoring

- ~~Extract reflector helper methods (declaring class, promoted property detection, etc.) into `AbstractAttribute`~~ — done (`getClassReflector()`, `getClassName()`, `getShortClassName()` on `AttributeInterface`/`AbstractAttribute`)
- ~~Extract shared test helpers (e.g. `assemble()`) into traits in `tests/Concerns`~~ — done (`AssemblesSpecification` trait)

### Documentation

- Add docblocks to augmenter pipes describing their configuration options (for generated reference docs)
- ~~Add class-level docblocks to DTOs with polymorphic subclasses (Operation, Parameter, Flow, Security\Scheme) showing common usage of specialized variants~~ — done

### Integration

- **AttributeEnricher** — extension point for frameworks (e.g. Nelmio) to translate non-OA attributes (like Symfony `#[Assert\*]`) into spec DTOs during assembly
- CompilerExtension support for vendor output (Attachable)
- Attributes inspired by openapi-extras (e.g. polymorphism helpers, additional validation keywords)

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
