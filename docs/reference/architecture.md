# 🧪 Spec Pipeline Architecture

This page documents the internals of the spec attributes pipeline — for users who want to understand how it works, write custom augmenters, or debug pipeline behavior.

## Pipeline overview

```
Source files → Assembler → Specification → Augmenters → Compiler → OpenAPI document
```

1. **Assembler** — scans source files, instantiates attributes from reflection, resolves nesting via slot maps (merge + hierarchical absorb)
2. **Specification** — a flat, typed container holding all collected attributes in buckets
3. **Augmenters** — enrich the specification with inferred data (types, refs, tags, etc.) via a grouped pipeline
4. **Compiler** — transforms the specification into a versioned OpenAPI document array
5. **Builder** — the unified entry point that orchestrates the pipeline

## Assembler

The Assembler collects spec attributes from source files and resolves their nesting relationships.

### Slot-map driven nesting

Each attribute declares its relationships via two methods:

- `merge()` returns `[TargetClass => 'slot']` — how this attribute composes into siblings on the same reflector
- `contained()` returns `[ParentClass => 'slot']` — which outer-level attribute types can absorb this attribute from inner reflector levels

Slots use `[]` suffix for collection append (`'parameters[]'`), bare name for scalar assignment (`'requestBody'`).

Both methods are child-driven: the attribute itself declares where it can go, whether same-level (merge) or cross-level (contained). This makes the system fully extensible by downstream code — custom attachables can declare their own nesting targets without modifying native attributes.

### Level-by-level resolution

Assembler resolution itself is purely attribute-relationship driven — no PHP structural semantics:

1. **Sibling merge** — attributes on the same reflector compose via `merge()` maps
2. **Hierarchical absorb** — resolved attributes flow upward level by level via `contained()` maps (first match wins). If a level has containers, unmatched non-roots are errors. If a level has no containers, unmatched attributes pass through to the next level up.

After resolution, only root attributes (should) remain and are added to the Specification.

### Root attributes

A "root" attribute is one that can exist independently in the Specification — it has its own bucket and doesn't require a parent container.

Always root: `Schema`, `Operation`, `PathItem`, `OpenApi`, `Info`, `Tag`, `Server`, `ExternalDocumentation`, `SecurityScheme`, `Components`, `Attachable`

Conditionally root: `Response` (when `response` key is set), `RequestBody` (when `request` key is set)

Never root: `Parameter`, `Header`, `Link`, `Example`, `MediaType`, `Property`, etc. — these must be nested inside a parent or wrapped in a `Components` container.

## Specification

The Specification is a flat, typed container with one bucket per root attribute type. It holds all attributes collected by the Assembler, organized by type (schemas, operations, pathItems, tags, etc.).

Augmenters read from and write to the Specification's buckets. The container is deliberately simple — no tree structure, no parent pointers. Cross-bucket relationships are resolved by augmenters using reflectors.

## Augmenters

Augmenters form a grouped pipeline that enriches the Specification in three ordered phases:

### Pipeline phases

| Phase | Purpose | Examples |
|---|---|---|
| **Resolve** | Infer data from PHP reflection and cross-bucket relationships | `Inheritance`, `Names`, `Enums`, `PathItems`, `Types`, `Refs`, `Shortcuts` |
| **Reduce** | Filter or remove entries | `PathFilter`, `Cleanup` |
| **Augment** | Add derived metadata | `MediaTypes`, `Docblocks`, `OperationIds`, `Tags`, `EnumDescriptions` |

Each augmenter implements `PipeInterface` and receives the full Specification. Augmenters within a phase run in registration order.

### Configuring augmenters

```php
$builder->withAugmenters(function (\OpenApi\Utils\Pipeline $pipeline) {
    // Get a typed reference to configure
    $pipeline->get(Augmenter\OperationIds::class)?->setHash(true);

    // Enable/disable
    $pipeline->get(Augmenter\Cleanup::class)?->setEnabled(false);

    // Insert before another
    $pipeline->insert(new CustomAugmenter(), Augmenter\Inheritance::class);

    // Remove entirely
    $pipeline->remove(Augmenter\EnumDescriptions::class);
});
```

### Writing a custom augmenter

A custom augmenter implements `PipeInterface`:

```php
use OpenApi\Utils\PipeInterface;
use OpenApi\Specification;
use OpenApi\Spec as OA;

class CustomAugmenter implements PipeInterface
{
    public function group(): string|\BackedEnum
    {
        return \OpenApi\Augmenter\Group::Augment;
    }

    public function __invoke(mixed $payload): mixed
    {
        foreach ($payload->schemas as $schema) {
            // enrich schemas...
        }

        // or

        // the walker will walk all attributes (including nested) of the specification
        $payload->getWalker()->visit(OA\Property::class, function (OA\Property $property) {
            // ...
        });

        // or walk all attributes with $ref set
        $payload->getWalker()->eachRef(function () {
            // $attribute->ref = ...
        });

        return $payload;
    }
}
```

## Compilers

Each OpenAPI version has its own compiler that handles version-specific output differences:

| Compiler | Version | Key differences                                                   |
|---|---|-------------------------------------------------------------------|
| `OpenApi30Compiler` | 3.0.x | `nullable` as property, `exclusiveMinimum` as boolean             |
| `OpenApi31Compiler` | 3.1.x | `nullable` via type array, `exclusiveMinimum` as number, webhooks |
| `OpenApi32Compiler` | 3.2.x | Extends 3.1 (currently without additional features)               |

The compiler transforms a Specification into a plain PHP array representing the OpenAPI document. Version selection is automatic based on `Builder::setVersion()` or the `#[OA\OpenApi(version: '...')]` attribute.

## Design principles

### Normalise early, store simple

When a property has both a rich input type (e.g. a PHP enum) and a plain serialization type (e.g. string), accept both on input but normalise to the plain type immediately in the constructor. Properties always store the simple form — enums, objects, and convenience types are input sugar only. This keeps downstream code (augmenters, compilers, serialization) free of type-checking branches.

```php
// Example: FlowType enum accepted on input, stored as string
public function __construct(string|FlowType|null $flow = null) {
    parent::__construct([
        'flow' => $flow instanceof \BackedEnum ? $flow->value : $flow,
    ]);
}
```

### Reflectors as glue

Every root DTO carries its originating reflector (`ReflectionClass`, `ReflectionMethod`, etc.). This is the fundamental mechanism for resolving cross-bucket relationships at augmentation time.

Key applications:

- **PathItem ↔ Operation binding** — PathItem is placed on a class; operations on methods of that class. The `PathItems` augmenter walks `ReflectionMethod::getDeclaringClass()` to find which PathItem governs an operation.
- **Prefix composition via inheritance** — PathItems on parent classes contribute prefixes. The augmenter walks `ReflectionClass::getParentClass()` to compose the full path prefix chain.
- **OperationId generation** — the reflector provides class/method name context for auto-generated identifiers.
- **Type inference** — the `Types` augmenter reads PHP type declarations from property/parameter reflectors.

This design keeps the Assembler simple (just collect into buckets) and makes cross-cutting relationships resolvable without coupling DTOs to each other.

### DTO class tree

All spec attributes extend `AbstractAttribute` and live in the `OpenApi\Spec` namespace:

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
│   ├── OA\Schema\AdditionalProperties
│   ├── OA\Schema\Items
│   ├── OA\Schema\Ref
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
│   ├── OA\Encoding
│   ├── OA\MediaType\Json
│   └── OA\MediaType\Xml
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

Typed subclasses (e.g. `Operation\Get`, `Parameter\Path`) pre-fill fields that the base class requires explicitly. The base class can always be used directly for full control.

## Classic processor mapping

How each classic processor maps to the new pipeline:

| Classic Processor | Spec Equivalent                          | Stage |
|---|------------------------------------------|---|
| ExpandClasses | `Inheritance` + Assembler                | augment + assembly |
| ExpandTraits | `Inheritance` + Assembler                | augment + assembly |
| ExpandInterfaces | `Inheritance` + Assembler                | augment + assembly |
| ExpandEnums | `Enums`                                  | augment |
| MergeIntoOpenApi | `Assembler`                                | assembly |
| MergeIntoComponents | Compiler                                 | compile |
| MergeJsonContent | `Shortcuts`                                | resolve |
| MergeXmlContent | `Shortcuts`               | resolve |
| BuildPaths | Compiler                                 | compile |
| AugmentSchemas | `Names` + `Types` + Assembler + Compiler | mixed |
| AugmentProperties | `Types`                                  | resolve |
| AugmentParameters | `Types`                                  | resolve |
| AugmentItems | `Types`                                  | resolve |
| AugmentRequestBody | `Types`                                  | resolve |
| AugmentRefs | `Refs`                                   | resolve |
| AugmentDiscriminators | `Refs`                                   | resolve |
| AugmentTags | `Tags`                                   | augment |
| AugmentMediaType | `MediaTypes`                             | augment |
| DocBlockDescriptions | `Docblocks`                              | augment |
| OperationId | `OperationIds`                           | augment |
| CleanUnmerged | Assembler (orphan validation)            | assembly |
| CleanUnusedComponents | `Cleanup`                                | reduce |
| PathFilter | `PathFilter`                             | reduce |

The key architectural difference: classic processors operate on a mutable annotation tree in a single chain. Spec augmenters operate on an immutable Specification with typed buckets, grouped into explicit phases.
