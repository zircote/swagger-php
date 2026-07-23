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

**Root attributes.** A "root" attribute is one that can exist independently in the Specification — it has its own bucket and doesn't require a parent container. After the two-pass assembly (merge + hierarchy resolution), every remaining attribute must satisfy `isRoot() === true` or the assembler throws an error.

Root DTOs have a corresponding bucket in `Specification` and are collected directly by the assembler:
- Always root: `Schema`, `Operation`, `PathItem`, `OpenApi`, `Info`, `Tag`, `Server`, `ExternalDocumentation`, `SecurityScheme`, `Components`, `Attachable`
- Conditionally root: `Response` (when `response` key is set), `RequestBody` (when `request` key is set)
- Never root: `Parameter`, `Header`, `Link`, `Example`, `MediaType`, `Property`, etc.

Non-root DTOs must be nested inside a parent (via `merge()`/`contains()` maps) or wrapped in a `Components` container. This is what makes `Components` necessary — it provides a root-level home for DTOs like Parameter and Header that cannot stand alone at class level.

**`isRoot()` is validation, not routing.** The `merge()` and `contains()` maps drive all nesting decisions — `isRoot()` does not control where an attribute ends up. Its role is purely a post-resolution assertion that catches mis-placed attributes early (e.g. a Parameter on a class without a sibling PathItem or Operation).

Custom DTOs should declare `merge()` targets to specify where they nest, and `isRoot()` to indicate whether they can land in a `Specification` bucket independently.

**Immutable-style attributes.** Spec attributes use constructor-promoted public properties. They carry no serialization logic — that responsibility belongs exclusively to compilers.

**Version-aware compilers.** Each OpenAPI version (3.0, 3.1, 3.2) has its own compiler that handles version-specific differences (nullable handling, exclusive min/max, webhooks, etc.) rather than branching inside a single serializer.

**Typed subclasses.** Common patterns get dedicated attribute classes to reduce boilerplate. The base classes (e.g. `OA\Operation`, `OA\Parameter`, `OA\Flow`) can always be used directly but require specifying fields that subclasses pre-fill — for example `#[OA\Operation(path: '/pets', method: 'get')]` vs `#[OA\Operation\Get(path: '/pets')]`.

```
OA\AbstractAttribute
├── OA\Components
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
├── OA\Security
│   ├── OA\Security\Requirement
│   └── OA\Security\Scheme
│       ├── OA\Security\Scheme\Http
│       ├── OA\Security\Scheme\ApiKey
│       ├── OA\Security\Scheme\OAuth2
│       ├── OA\Security\Scheme\OpenIdConnect
│       └── OA\Security\Scheme\MutualTls
└── OA\Attachable
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

### Full programmatic Builder example

```php
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Utils\AttributeFactory;
use OpenApi\Augmenter;

$result = (new Builder())
    ->setMode(Mode::SPEC)
    ->setVersion('3.1.0')
    ->addSource('src/Api')

    // Configure the attribute factory — add custom translators
    ->withAttributeFactory(function (AttributeFactory $factory): void {
        // Add a translator that converts Symfony validation attributes
        // into OpenAPI schema constraints
        $factory->getTranslators()->add(new SymfonyValidationTranslator());
    })

    // Configure the augmenter pipeline — add/remove/reorder pipes
    ->withAugmenters(function (\OpenApi\Utils\Pipeline $pipeline): void {
        // Disable cleanup (keep all components even if unreferenced)
        $pipeline->get(Augmenter\Cleanup::class)?->setEnabled(false);

        // Configure operationId generation to use hashing
        $pipeline->get(Augmenter\OperationIds::class)?->setHash(true);

        // Filter to only specific paths/tags
        $pipeline->get(Augmenter\PathFilter::class)
            ?->setPathFilter('/^\/api\/v2/')
            ?->setTagFilter('/^(Users|Products)$/');

        // Insert a custom augmenter before Inheritance
        $pipeline->insert(new CustomAugmenter(), Augmenter\Inheritance::class);

        // Remove an augmenter entirely
        $pipeline->remove(Augmenter\EnumDescriptions::class);
    })

    // Classic-mode only: configure the underlying Generator
    // ->withGenerator(function (\OpenApi\Generator $generator): void {
    //     $generator->setProcessors([...]);
    // })

    ->build();

// Access results
$openapi = $result->openapi;     // compiled OpenAPI array
$yaml = $result->toYaml();       // YAML string
$json = $result->toJson();       // JSON string
$warnings = $result->warnings(); // any non-fatal issues
```

## What's done

- All core spec DTOs (OpenApi, Info, Server, Tag, Operation, Schema, Parameter, Response, Header, PathItem, etc.)
- Assembler with two-pass nesting resolution
- `AttributeFactory` extracted from Assembler for standalone attribute instantiation, with `TypedList<AttributeTranslatorInterface>` translators and `withTranslators()` hook
- `TypedList<T>` generic collection (implements `IteratorAggregate`) — base class for `Pipeline`, also used for translator management
- Three compilers (3.0, 3.1, 3.2) with version-specific handling and shared inheritance
- Builder with tri-mode support (classic/spec/hybrid), CLI integration, and callable hooks (`withAttributeFactory()`, `withAugmenters()`, `withGenerator()`)
- `HybridBridge` iterates `Analysis` annotations directly into `Specification` DTOs (no tree assembly needed)
- Security namespace (Scheme subclasses + Requirement)
- Typed subclasses for Parameter, Flow, and Operation
- All examples ported to spec attributes (see table below)
- Pipeline classes tested (Assembler, Compilers, Builder)
- Augmenter infrastructure: `PipeInterface` with `@template` generics, `Pipeline` (extends `TypedList`) with grouping (resolve → reduce → augment), `Pipeline::get()` for typed configuration
- `SpecificationWalker` — instance-based tree traversal with unified schema descent
- All augmenters implemented (see table below)
- Shared `Type\TypeResolver` core producing `SchemaType` value objects — used by both the spec-attributes `Types` augmenter and the classic `TypeInfoTypeResolver`, confirming identical type resolution behavior
- `Attachable` DTO with `Specification::$attachables` bucket, inline `$attachables` parameter on all DTOs, slot validation in `AttributeFactory::nestChild()`

### Augmenter status

| Augmenter | Group | Description | Status |
|---|---|---|---|
| `Types` | resolve | Infers schema type, format, nullable, items, refs from PHP types and docblocks | Done |
| `Refs` | resolve | Resolves FQCN `$ref` values to JSON Reference paths, including discriminator mappings | Done |
| `Docblocks` | augment | Summary, description, deprecated from PHPDoc | Done |
| `OperationIds` | augment | Generates operationId from reflector context, with `hash` option | Done |
| `Tags` | augment | Auto-generates global tags from operation usage | Done |
| `Inheritance` | augment | Trait/interface allOf composition, non-schema interface member merging | Done |
| `Enums` | augment | Resolves PHP enum backing types into schema enum/type values | Done |
| `Names` | augment | Auto-names component keys from reflector class/context | Done |
| `MediaTypes` | augment | Re-keys encoding by property name | Done |
| `Cleanup` | reduce | Removes unreferenced components (iterative, handles nested deps) | Done |
| `PathFilter` | reduce | Filters operations by tag/path regex patterns | Done |
| `EnumDescriptions` | augment | Generates descriptions for enum-based properties (BETA, disabled by default) | Done |
| `PathItems` | resolve | Prefix composition, clone-down of tags/security/responses, path inference | Done |

## Test coverage

* New code is covered by new tests.
* All examples tests pass in all modes (`CLASSIC`, `HYBRID` and `SPEC`). Spec versions have been added to all examples.
* All tests that now rely on `Generator` or `AbstractAnnotation` specific features are passing in `HYBRID` mode.
* Spec version of the performance test is passing and way faster than classic.

## Classic processor mapping

How each classic processor maps to the new pipeline:

| Classic Processor | Spec-Attributes Equivalent | Stage | Status |
|---|---|---|---|
| ExpandClasses | `Inheritance` + Assembler (`contains()` maps) | augment + assembly | Done |
| ExpandTraits | `Inheritance` + Assembler (`contains()` maps) | augment + assembly | Done |
| ExpandInterfaces | `Inheritance` + Assembler (`contains()` maps) | augment + assembly | Done |
| ExpandEnums | `Enums` | augment | Done |
| MergeIntoOpenApi | Assembler (builds Specification) | assembly | Done |
| MergeIntoComponents | Compiler (groups into components) | compile | Done |
| MergeJsonContent | N/A — attribute eliminated | — | N/A |
| MergeXmlContent | N/A — attribute eliminated | — | N/A |
| BuildPaths | Compiler (groups by path) | compile | Done |
| AugmentSchemas | Split (see below) | mixed | Done |
| AugmentProperties | `Types` (type/format/nullable + property name) | resolve | Done |
| AugmentParameters | `Types` (type/name/required inference) | resolve | Done |
| AugmentItems | `Types` (array items via SchemaType.items) | resolve | Done |
| AugmentRequestBody | `Types` (required inference from nullable) | resolve | Done |
| AugmentRefs | `Refs` (FQCN → JSON reference) | resolve | Done |
| AugmentDiscriminators | `Refs` (discriminator mapping resolution) | resolve | Done |
| AugmentTags | `Tags` (auto-generate tags from operations) | augment | Done |
| AugmentMediaType | `MediaTypes` (re-key encoding by property name) | augment | Done |
| DocBlockDescriptions | `Docblocks` (summary/description/deprecated) | augment | Done |
| OperationId | `OperationIds` | augment | Done |
| CleanUnmerged | Assembler (orphan validation in resolveHierarchy) | assembly | Done |
| CleanUnusedComponents | `Cleanup` (iterative, handles nested deps) | reduce | Done |
| PathFilter | `PathFilter` | reduce | Done |

**AugmentSchemas split:** This processor's responsibilities are distributed across four concerns:
1. Schema naming from class/trait/interface/enum → `Names` (done)
2. `type: object` inference when properties present → `Types` (done)
3. Property merging into parent schema → Assembler via `merge()`/`contains()` (done)
4. allOf merge when both properties + allOf exist → Compiler (done)

## Inheritance expansion

### Authoritative rules (derived from classic behavior)

These rules define the agreed upon inheritance behavior. The classic path implements them via TokenScanner-filtered definitions; the spec path must produce identical results.

The rules mirror PHP inheritance: a schema inherits everything its PHP class inherits. The same rule applies uniformly to parents, traits, and interfaces.

#### Property ownership

Each property belongs to exactly one source — the class-like that physically declares it:

- A class's **own properties** are those declared in its class body (including promoted constructor parameters), but **not** properties contributed by traits.
- Trait properties belong to the trait, not to the class that uses it.
- Parent class properties belong to the parent.

This matches PHP's source-level declaration. Trait properties appear on the using class at runtime, but for schema purposes they belong to the trait.

#### The one rule

For each parent, trait, or interface that a schema's class relates to:

- **Has a schema** → add `$ref` to `allOf` (composition via reference)
- **Has no schema** → merge its own members into the current schema (inlining)

This rule is the same for all three relationship types. The only differences are PHP language constraints:

- **Interfaces** can only contribute methods (PHP interfaces cannot declare properties)
- **Parents** are walked linearly; stop at the first ancestor with a schema (everything above is inherited transitively through that ref)
- **Traits on non-schema ancestors** are also processed (stop at the first ancestor with a schema)

#### Deduplication of merged properties

A running list of already-merged property names prevents duplicates. A property is merged only if its name is not already present on the schema.

#### allOf refs are not deduplicated

Refs are added for each direct relationship, without checking whether the referenced schema is already reachable transitively through another ref. This is unavoidable — we compose with predefined schemas and cannot inspect their contents to determine overlap.

Example:
```php
#[Schema(schema: "Timestamps")]
trait HasTimestamps { public string $createdAt; }

#[Schema]
class BaseModel { use HasTimestamps; public int $id; }

#[Schema]
class User extends BaseModel { use HasTimestamps; public string $email; }
```

Output for `User`:
```yaml
User:
  allOf:
    - $ref: '#/components/schemas/BaseModel'    # parent (which itself refs Timestamps)
    - $ref: '#/components/schemas/Timestamps'   # direct trait usage
    - properties: { email: ... }               # own properties only
```

The `$ref: Timestamps` is semantically redundant (already composed via BaseModel) but present because User explicitly declares `use HasTimestamps`. The alternative — inspecting ref targets for transitive overlap — would require resolving the full schema graph and is not feasible at this stage.

#### allOf finalization

After inheritance expansion, if a schema has both `allOf` refs **and** own properties, the properties are wrapped in a dedicated `allOf` entry (`type: object`) to produce a pure allOf composition.

### Reflection limitation: trait member ownership

PHP reflection lies about trait members: `getDeclaringClass()` for a trait property returns the **using class**, not the trait. This means `ReflectionClass::getProperties()` + `getDeclaringClass()` cannot distinguish a class's own properties from trait-contributed ones.

```php
trait T { public string $x; }
class C { use T; public string $y; }
// ReflectionClass("C")->getProperty("x")->getDeclaringClass()->getName() === "C"
```

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

### Augmenter: `PathItems` (resolve group, early)

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

## DTO: `OA\Components`

`#[OA\Components]` is a class-level attribute that acts as a container for reusable component definitions that cannot stand on their own as root attributes.

The Components attribute itself is never emitted in the output. During assembly, its children are promoted directly into the Specification's top-level buckets, just as if they had been collected from a Schema or PathItem class.

### When to use

The primary use case is for DTOs that are **not roots** and therefore cannot be declared at class level on their own:

- **Parameter** — shared path/query/header parameters (pagination, tenant ID)
- **Header** — shared response headers (rate limiting, correlation IDs)
- **Link** — reusable link definitions
- **Example** — shared examples

Other types (Schema, PathItem, SecurityScheme, named Response, named RequestBody) are already roots and can be declared directly on a class without needing a Components wrapper. They are accepted by Components for completeness but don't require it.

### Example

```php
#[OA\Components]
class SharedComponents {
    #[OA\Parameter(parameter: 'page', name: 'page', in: 'query', schema: new OA\Schema(type: 'integer'))]
    public int $page;

    #[OA\Parameter(parameter: 'per_page', name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer'))]
    public int $perPage;

    #[OA\Header(header: 'X-Rate-Limit', description: 'Requests remaining', schema: new OA\Schema(type: 'integer'))]
    public string $rateLimit;

    #[OA\Example(example: 'dog', summary: 'A dog example', value: ['name' => 'Fido'])]
    public string $dogExample;
}
```

These components can then be referenced via `$ref` from operations, PathItems, or other DTOs.

## DTO: `OA\Schema`

The spec version of `OA\Schema` is a fully standalone attribute. No other attributes inherits from it - its all down to composition.
Side effect is that `OA\Schema` now can be stacked next to other attributes (as long as it is not ambiguous).

Inline nesting works also, as before.

### Example

```php
#[OA\Schema]
class Model {
    #[OA\Property]
    #[OA\Schema(type: 'integer')]
    public int $page;
}
```

## What's next

### Hybrid mode hardening

- Dedicated test coverage for `HybridBridge` (currently exercised only indirectly via examples)
- Migrate scratch tests and doc snippet tests to run in spec and/or hybrid modes

### Testing

- Adopt more tests for the spec pipeline: scratch tests, snippet tests, and edge-case scenarios currently only exercised via classic mode
- Ensure augmenter interactions are covered end-to-end (not just unit-level)
- Identify tests that produce spec YAML output and validate them against the OpenAPI spec using redocly
- Augmenter tests should persist expected YAML fixtures where appropriate

### Documentation

- Dedicated docs session — refine augmenter reference, usage guide, and generated docs
- Add docblocks to augmenter pipes describing their configuration options (for generated reference docs)

### Extension systems

Three extension points:

- ~~**`AttributeTranslatorInterface`**~~ — **Done.** Hook into assembly to translate non-OA attributes (e.g. Symfony `#[Assert\*]`, framework route annotations) into spec DTOs. Runs per-element during the factory/assembler phase. Translators are registered on `AttributeFactory` via `getTranslators()->add()` or `withTranslators()`. Each translator implements two methods: `getAttributes()` (collect `ReflectionAttribute` instances from a reflector) and `translate()` (transform the cumulative list of instantiated objects into `AttributeInterface` instances). The default `DefaultAttributeTranslator` handles native OA attributes; custom translators are appended and receive the accumulated result from prior translators.
- ~~**`Attachable` DTO**~~ — **Done.** `OA\Attachable` extends `AbstractAttribute`, is repeatable on any target, and `isRoot() === true` by default (gets its own `Specification::$attachables` bucket). Can also be inlined into any attribute via the `$attachables` constructor parameter or nested via custom `merge()` maps. Slot validation in `AttributeFactory::nestChild()` catches invalid merge targets early. Custom attachables subclass `OA\Attachable` and override `merge()` to specify where they nest.
- ~~**`CompilerExtension`**~~ — Abandoned for now. Since compiler can easiy be extended/swapped it feels overkill with the more powerfull alternatives of attribute translation and `OA\Attribute`

These extension systems should also address downstream integration needs (e.g. Nelmio translating Symfony metadata, framework route annotations) without requiring those projects to couple to pipeline internals.

### Shortcut attributes

Re-evaluate support for convenience attributes that reduce boilerplate in common patterns:

- **`Items`** — shorthand for array item schema declaration; this probably should be extending `OA\Items` and get a dedicated `PipeInterface` augmenter.
- **`JsonContent`** / **`XmlContent`** — shorthand for wrapping a schema in a media type with the appropriate content type; a new `AttributeTranslatorInterface` should be implemented to handle the translation of these attributes.

These could be implemented as assembler-level transforms (expand the shortcut into canonical DTOs during assembly). This would serve as both useful functionality and documentation of how the extension systems work in practice.

Need to document the general pattern for shortcut attributes: how they participate in nesting (via `merge()`), when they expand (assembly vs augmentation), and how custom shortcuts can be added by users.

### Additional augmenter pipes

None planned at this time.

### Shipping

- Migration guide and dual-tab documentation
- Architecture reference document — comprehensive guide covering the full pipeline, extension points, and design rationale
- Mark spec attributes as beta during v6 to allow iteration
- Deprecation path for classic annotations in v7

### Version timeline

- **v6.x** — spec pipeline ships as opt-in (`setMode('spec')`). Classic remains default. Both modes available side-by-side.
- **v7** — spec mode becomes default. Classic still available via `setMode('classic')`. Remove legacy namespaces (`Annotations\*`, `Attributes\*`), `Context`, `Analysis`, doctrine support. Introduce `ProcessorInterface::process(Specification)`.
- **v8** — classic mode removed entirely. Builder is spec-only.

## Consequences

- Users get a cleaner, more discoverable API with IDE autocompletion on typed attributes
- The compilation step is now testable in isolation (pure function: Specification → array)
- Version-specific logic is contained in individual compilers rather than scattered across processors
- The two-pass assembly model is simpler to reason about than the current processor chain
- Augmenters can be developed and tested independently of the core pipeline
