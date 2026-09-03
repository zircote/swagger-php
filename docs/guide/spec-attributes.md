# 🧪 Using Spec Attributes

::: warning Beta
Spec attributes are mostly feature-complete but still beta. The API may evolve based on feedback before being promoted to the default mode in a future major version.
:::

Spec attributes are a new way to annotate your PHP code for OpenAPI generation. They live in the `OpenApi\Spec` namespace and are typed data containers: unlike classic annotations they carry no serialization logic, and the augmenter pipeline fills in derived values before compilation.

## Namespace

```php
use OpenApi\Spec as OA;
```

This replaces the classic `use OpenApi\Attributes as OA;`. The `OA` alias keeps your code familiar.

## Basic example

A minimal working API with spec attributes:

```php
use OpenApi\Spec as OA;

#[OA\OpenApi(version: '3.1.0')]
#[OA\Info(title: 'My API', version: '1.0.0')]
#[OA\Server(url: 'https://api.example.com')]
class OpenApiSpec {}
```

```php
use OpenApi\Spec as OA;

#[OA\Schema]
class Pet
{
    #[OA\Property]
    public int $id;

    #[OA\Property]
    public string $name;

    #[OA\Property]
    public ?string $tag;
}
```

```php
use OpenApi\Spec as OA;

class PetController
{
    #[OA\Operation\Get(path: '/pets', operationId: 'listPets', tags: ['pets'])]
    #[OA\Response(
        response: 200,
        description: 'A list of pets',
        content: [new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(type: 'array', items: new OA\Schema(ref: Pet::class)),
        )],
    )]
    public function list() {}

    #[OA\Operation\Get(path: '/pets/{petId}', operationId: 'showPet', tags: ['pets'])]
    #[OA\Parameter(name: 'petId', in: 'path', required: true)]
    #[OA\Response(
        response: 200,
        description: 'A single pet',
        content: [new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: Pet::class))],
    )]
    public function show(int $petId) {}
}
```

Generate the spec:

```php
use OpenApi\Builder;
use OpenApi\Builder\Mode;

$result = (new Builder())
    ->setMode(Mode::SPEC)
    ->addSource('src/')
    ->build();

echo $result->toYaml();
```

Or via CLI:

```shell
./vendor/bin/openapi src/ --mode spec
```

## Schemas

Place `#[OA\Schema]` on a class to define a schema component. Properties are declared with `#[OA\Property]` on class properties.

```php
use OpenApi\Spec as OA;

#[OA\Schema(title: 'User', required: ['email'])]
class User
{
    #[OA\Property]
    #[OA\Schema(format: 'int64')]
    public int $id;

    #[OA\Property]
    public string $email;

    #[OA\Property]
    public ?string $name;
}
```

Types, formats, and nullability are inferred from PHP type declarations by the `Types` augmenter. You only need to specify `#[OA\Schema(...)]` on a property when you want to override the inferred values (e.g. format, description, example).

### Stacking Schema and Property

In spec mode `OA\Property` no longer extends `OA\Schema`; both derive directly from `AbstractAttribute`. This means you can stack `#[OA\Property]` and `#[OA\Schema]` on the same target:

```php
#[OA\Property(property: 'status')]
#[OA\Schema(type: 'string', enum: ['active', 'inactive'])]
public string $status;
```

`#[OA\Property]` declares that this is a property of the parent schema. `#[OA\Schema]` provides the property's type definition. When no explicit `#[OA\Schema]` is present, the type is inferred from the PHP declaration.

## Operations

Operations map to HTTP methods on API endpoints. Spec mode provides typed subclasses for each HTTP method:

```php
#[OA\Operation\Get(path: '/pets')]
#[OA\Operation\Post(path: '/pets')]
#[OA\Operation\Put(path: '/pets/{id}')]
#[OA\Operation\Delete(path: '/pets/{id}')]
#[OA\Operation\Patch(path: '/pets/{id}')]
#[OA\Operation\Head(path: '/pets')]
#[OA\Operation\Options(path: '/pets')]
#[OA\Operation\Trace(path: '/pets')]
```

You can also use the base class with an explicit method:

```php
#[OA\Operation(path: '/pets', method: 'get')]
```

### Responses and parameters

Responses and parameters can be nested inside the operation or placed as separate attributes on the same method:

```php
// Nested
#[OA\Operation\Get(path: '/pets/{id}', responses: [
    new OA\Response(response: 200, description: 'OK'),
    new OA\Response(response: 404, description: 'Not found'),
])]
public function show(int $id) {}

// Flat (equivalent — merged automatically)
#[OA\Operation\Get(path: '/pets/{id}')]
#[OA\Response(response: 200, description: 'OK')]
#[OA\Response(response: 404, description: 'Not found')]
public function show(int $id) {}
```

The flat form extends to deeper nesting: a `MediaType` and `Schema` stacked alongside a `Response` merge into it, in any declaration order.

```php
#[OA\Operation\Get(path: '/pets/{id}')]
#[OA\Response(response: 200, description: 'OK')]
#[OA\MediaType(mediaType: 'application/json')]
#[OA\Schema(type: 'string')]
public function show(int $id) {}
```

The merge is by attribute type, so the flat form only works while it is unambiguous. Two `Response` siblings each expecting their own `MediaType`, or a `MediaType` next to both a `Response` and a `RequestBody`, fail with an `Ambiguous merge` error — nest those inline instead.

### Parameters on method arguments

Parameters can be placed directly on method arguments:

```php
#[OA\Operation\Get(path: '/pets/{petId}')]
public function show(
    #[OA\Parameter(name: 'petId', in: 'path', required: true)]
    int $petId
) {}
```

Typed parameter subclasses reduce boilerplate:

```php
#[OA\Operation\Get(path: '/pets/{petId}')]
public function show(
    #[OA\Parameter\Path(name: 'petId')]
    int $petId
) {}
```

Available subclasses: `OA\Parameter\Path`, `OA\Parameter\Query`, `OA\Parameter\Header`, `OA\Parameter\Cookie`.

## PathItem

`#[OA\PathItem]` on a class groups operations with shared configuration. It enables prefix composition via class inheritance and pushes shared metadata down to operations.

```php
use OpenApi\Spec as OA;

#[OA\PathItem(prefix: '/api/v1')]
class BaseController {}

#[OA\PathItem(prefix: '/users', tags: ['Users'])]
class UserController extends BaseController
{
    #[OA\Operation\Get(path: '/')]
    public function list() {}

    #[OA\Operation\Get(path: '/{id}')]
    public function show(int $id) {}
}
```

This produces paths `/api/v1/users/` and `/api/v1/users/{id}`, both tagged with `Users`.

### What PathItem provides

| Feature                   | Description                                            |
| ------------------------- | ------------------------------------------------------ |
| `prefix`                  | Composable path prefix, inherited via class hierarchy  |
| `tags`                    | Cloned to all operations that don't declare their own  |
| `security`                | Cloned to all operations that don't declare their own  |
| `responses`               | Cloned to all operations (e.g. shared error responses) |
| `parameters`              | Emitted as path-level parameters in the output         |
| `summary` / `description` | Emitted at path level                                  |
| `servers`                 | Emitted at path level                                  |

## Shortcuts
### MediaType

`OA\MediaType\Json` and `OA\MediaType\Xml` are the corresponding versions of the classic `OA\JsonContent` and `OA\XmlContent`, respectively.

They do work pretty much the same, however since they are not inheriting from `OA\Schema`, they only support a limited (most common) set of schema attributes.
Still useful even when nesting `OA\Schema` as the media type is prefilled either way.

**If a nested `OA\Schema` is set, the custom attributes are ignored.**

```php
// Without shortcut
#[OA\Response(response: 200, content: [
    new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(type: 'array', items: new OA\Schema(ref: Pet::class))),
])]

// With shortcut
#[OA\Response(response: 200, content: [new OA\MediaType\Json(type: 'array', items: new OA\Schema(ref: Pet::class))])]
```

### Items
`#[OA\Schema\Items]` is the equivalent to the classic `OA\Items` attribute. Similar to `OA\MediaType\Json` and `Xml` it is a shortcut that allows to skip the outer `OA\Schema(type: 'array')`. The `Shortcuts` augmenter wraps it into `OA\Schema(type: 'array', items: ...)` automatically.

Since `OA\Schema\Items` extends `OA\Schema`, the [implicit `OA\Property`](#property-spec-only) shortcut applies — you can omit the explicit `#[OA\Property]` attribute:

```php
// Verbose
#[OA\Property]
#[OA\Schema(type: 'array', items: new OA\Schema(ref: Tag::class))]
public array $tags;

// With Items shortcut
#[OA\Property]
#[OA\Schema\Items(ref: Tag::class)]
public array $tags;

// With Items shortcut + implicit Property
#[OA\Schema\Items(ref: Tag::class)]
public array $tags;
```

Both the `MediaType` and `Items` shortcuts are resolved by the `Shortcuts` augmenter.

### Encoded
`OA\Property\Encoded` bundles a property definition with its encoding — instead of declaring `OA\Encoding` separately on the `OA\MediaType`, you can inline it directly on the property. The `MediaTypes` augmenter promotes the encoding to the parent MediaType automatically.

If no `encoding` property name is set on the nested `OA\Encoding`, it defaults to the property name.

```php
// Without shortcut — encoding declared separately on the MediaType
#[OA\Response(response: 200, content: [
    new OA\MediaType\Json(
        properties: [
            new OA\Property(property: 'avatar', schema: new OA\Schema(type: 'object')),
        ],
        encoding: [
            new OA\Encoding(encoding: 'avatar', contentType: 'image/png, image/jpeg'),
        ],
    ),
])]

// With shortcut — encoding bundled with the property
#[OA\Response(response: 200, content: [
    new OA\MediaType\Json(
        properties: [
            new OA\Property\Encoded(
                property: 'avatar',
                schema: new OA\Schema(type: 'object'),
                encoding: new OA\Encoding(contentType: 'image/png, image/jpeg'),
            ),
        ],
    ),
])]
```

The `Encoded` shortcut is resolved by the `MediaTypes` augmenter.

### AdditionalProperties
`OA\Schema\AdditionalProperties` is a typed alias for `OA\Schema` — functionally identical but improves readability when declaring `additionalProperties` constraints:

```php
#[OA\Schema(
    type: 'object',
    properties: [
        new OA\Property(
            property: 'errors',
            schema: new OA\Schema(
                type: 'object',
                additionalProperties: new OA\Schema\AdditionalProperties(
                    type: 'array',
                    items: new OA\Schema\Items(type: 'string'),
                ),
            ),
        ),
    ],
)]
class ValidationErrors {}
```

## Components

`#[OA\Components]` is a class-level container for reusable definitions that cannot stand alone as root attributes — primarily Parameters, Headers, Links, and Examples.

```php
use OpenApi\Spec as OA;

#[OA\Components]
class SharedComponents
{
    #[OA\Parameter(parameter: 'page', name: 'page', in: 'query')]
    #[OA\Schema(type: 'integer', default: 1)]
    public int $page;

    #[OA\Parameter(parameter: 'per_page', name: 'per_page', in: 'query')]
    #[OA\Schema(type: 'integer', default: 20)]
    public int $perPage;

    #[OA\Header(header: 'X-Rate-Limit', description: 'Requests remaining')]
    #[OA\Schema(type: 'integer')]
    public string $rateLimit;
}
```

These can then be referenced from operations via `$ref`:

```php
#[OA\Operation\Get(path: '/users', parameters: [
    new OA\Parameter(ref: '#/components/parameters/page'),
    new OA\Parameter(ref: '#/components/parameters/per_page'),
])]
public function list() {}
```

::: tip When to use Components
Schemas, PathItems, security schemes (`OA\Security\Scheme`), and named Responses/RequestBodies are root attributes — they can be declared directly on a class without a Components wrapper. Use Components only for types that can't stand alone (Parameter, Header, Link, Example).
:::

## Inheritance

Spec attributes support schema composition via PHP class hierarchy. The rules mirror PHP inheritance:

- **Parent has a schema** → add `$ref` to `allOf`
- **Parent has no schema** → merge its properties inline
- Same rule applies to traits and interfaces

```php
use OpenApi\Spec as OA;

#[OA\Schema]
class BaseModel
{
    #[OA\Property]
    public int $id;

    #[OA\Property]
    public string $createdAt;
}

#[OA\Schema]
class User extends BaseModel
{
    #[OA\Property]
    public string $email;

    #[OA\Property]
    public string $name;
}
```

Output for `User`:
```yaml
User:
  allOf:
    - $ref: '#/components/schemas/BaseModel'
    - type: object
      properties:
        email:
          type: string
        name:
          type: string
```

### Traits

Traits with `#[OA\Schema]` are composed via `$ref` in `allOf`. Traits without a schema have their properties merged inline.

```php
#[OA\Schema]
trait HasTimestamps
{
    #[OA\Property]
    public string $createdAt;

    #[OA\Property]
    public string $updatedAt;
}

#[OA\Schema]
class Post
{
    use HasTimestamps;

    #[OA\Property]
    public string $title;
}
```

Output for `Post`:
```yaml
Post:
  allOf:
    - $ref: '#/components/schemas/HasTimestamps'
    - type: object
      properties:
        title:
          type: string
```

## Security

Security schemes use typed subclasses:

```php
use OpenApi\Spec as OA;

#[OA\Security\Scheme\Http(securityScheme: 'bearerAuth', scheme: 'bearer')]
#[OA\Security\Scheme\ApiKey(securityScheme: 'apiKey', name: 'X-API-Key', in: 'header')]
#[OA\Security\Scheme\OAuth2(securityScheme: 'oauth2', flows: [
    new OA\Flow\AuthorizationCode(
        authorizationUrl: 'https://auth.example.com/authorize',
        tokenUrl: 'https://auth.example.com/token',
        scopes: ['read:pets' => 'Read pets', 'write:pets' => 'Write pets'],
    ),
])]
class OpenApiSpec {}
```

Apply security to operations:

```php
#[OA\Operation\Get(path: '/pets', security: [
    new OA\Security\Requirement(scheme: 'bearerAuth'),
])]
public function list() {}
```

Or apply globally via the OpenApi attribute or via PathItem (cloned to all operations).

## Differences from classic attributes

| Classic (`OpenApi\Attributes`)           | Spec (`OpenApi\Spec`)                                                              |
| ---------------------------------------- | ---------------------------------------------------------------------------------- |
| `use OpenApi\Attributes as OA;`          | `use OpenApi\Spec as OA;`                                                          |
| `#[OA\Get(path: '/pets')]`               | `#[OA\Operation\Get(path: '/pets')]`                                               |
| `#[OA\JsonContent(...)]`                 | `new OA\MediaType\Json(...)` - but with limited set of (OA\Schema) attributes only |
| `#[OA\PathParameter(...)]`               | `#[OA\Parameter\Path(...)]`                                                        |
| `#[OA\Items(...)]`                       | `#[OA\Schema\Items(...)]`                                                          |
| `Property extends Schema`                | `Property` and `Schema` are siblings, stackable                                    |
| Processors walk a nested annotation tree | Augmenters enrich a flat set of typed buckets                                      |
| Single serializer with version branches  | Dedicated compiler per OpenAPI version                                             |

## Other differences

Beyond the API changes listed above, spec mode produces slightly different output in some cases:

### Explicit `type: object` on `allOf` schemas

When a class-based schema uses `allOf`, spec mode emits an explicit `type: object` on the schema itself. Classic mode omits it.

```php
#[OA\Schema(
    schema: 'create-user',
    allOf: [
        new OA\Schema(ref: '#/components/schemas/abstract-user'),
        new OA\Schema(required: ['name', 'email']),
    ]
)]
class CreateUser extends AbstractUser {}
```

Classic output:
```yaml
create-user:
  allOf:
    - $ref: '#/components/schemas/abstract-user'
    - required:
        - name
        - email
```

Spec output:
```yaml
create-user:
  type: object
  allOf:
    - $ref: '#/components/schemas/abstract-user'
    - required:
        - name
        - email
```

This is semantically stricter — the schema explicitly declares it must be an object, rather than leaving the type to be inferred from the `allOf` members.

### Duplicate `$ref` deduplication in `allOf`

When a class extends a parent that has its own schema, the `Inheritance` augmenter adds a `$ref` to the parent in `allOf`. If you also declare that same `$ref` explicitly, spec mode deduplicates it — only one entry survives. Classic mode may emit the same `$ref` twice.

Reusing the `CreateUser` example above: it both extends `AbstractUser` and names `abstract-user` in its own `allOf`. In spec mode that `$ref` appears once rather than twice — `Refs::dedupAllOfRefs()` drops the duplicate.

### Single-element `type` arrays reduced to string

When a schema's `type` is an array with a single element (e.g. `['string']`), spec mode compiles it as a plain string (`type: 'string'`). Classic mode may emit the array form.

```php
#[OA\Property]
#[OA\Schema(type: ['string'])]
public string $name;
```

Classic output:
```yaml
name:
  type:
    - string
```

Spec output:
```yaml
name:
  type: string
```

Both forms are valid in OpenAPI 3.1+, but the scalar form is more conventional for single types.

### No `requestBody` on `Get`, `Head`, `Options`, `Trace`

In spec mode, the typed operation subclasses `OA\Operation\Get`, `OA\Operation\Head`, `OA\Operation\Options`, and `OA\Operation\Trace` do not accept a `requestBody` parameter. This enforces the HTTP semantics where request bodies are not defined for these methods.

Classic mode accepts `requestBody` on all operations (with a comment noting it should be ignored by validators for methods that don't support it). In spec mode the parameter simply does not exist, so passing it raises `Error: Unknown named parameter $requestBody` when the attribute is instantiated.

```php
// Works in classic mode; a PHP error in spec mode, because
// Operation\Get::__construct() has no $requestBody parameter:
#[OA\Operation\Get(path: '/pets', requestBody: new OA\RequestBody(...))]

// Use Post, Put, Patch, or Delete for request bodies:
#[OA\Operation\Post(path: '/pets', requestBody: new OA\RequestBody(...))]
```

### Nullable `$ref` does not duplicate `description`

When a schema combines a `$ref` with `nullable: true` and a `description`, classic mode emits the `description` both on the `$ref` entry inside `oneOf` and as a sibling of `oneOf`. Spec mode only emits it on the `$ref` entry.

```php
#[OA\Schema(
    ref: '#/components/schemas/repository',
    description: 'The repository',
    nullable: true,
)]
```

Classic output (3.1+):
```yaml
oneOf:
  - { $ref: '#/components/schemas/repository', description: 'The repository' }
  - { type: 'null' }
description: 'The repository'
```

Spec output (3.1+):
```yaml
oneOf:
  - { $ref: '#/components/schemas/repository', description: 'The repository' }
  - { type: 'null' }
```

### Trait property ordering

When traits without their own `#[OA\Schema]` are merged inline, the property order in the output may differ between modes. Spec mode orders properties by trait `use` declaration order, which may place trait properties differently than classic mode.

### Nullable type inference from PHP types

Spec mode consistently infers nullability from PHP type declarations (e.g. `?\DateTime`). For OpenAPI 3.0 this adds `nullable: true`; for 3.1+ it emits `type: ['string', 'null']`. Classic mode may not infer nullability in all cases where the PHP type is nullable.

```php
#[OA\Property]
#[OA\Schema(format: 'date-time', type: 'string', readOnly: true)]
public ?\DateTime $deleted_at;
```

Classic output (3.0):
```yaml
deleted_at:
  type: string
  format: date-time
  readOnly: true
```

Spec output (3.0):
```yaml
deleted_at:
  type: string
  format: date-time
  nullable: true
  readOnly: true
```

## References

`$ref` values can use PHP class references (resolved by the `Refs` augmenter):

```php
#[OA\Response(
    response: 200,
    content: [new OA\MediaType(
        mediaType: 'application/json',
        schema: new OA\Schema(ref: Pet::class),
    )],
)]
```

The FQCN is resolved to the appropriate `#/components/schemas/Pet` JSON reference at augmentation time.

### Schema\Ref

`OA\Schema\Ref` is a restricted `Schema` subclass that only accepts `ref`, `title`, and `description`. It can be used in two ways:

**As a schema** — when you want a reference-only schema with optional metadata overrides (OpenAPI 3.1+):

```php
#[OA\Property(schema: new OA\Schema\Ref(ref: Pet::class, description: 'The pet'))]
public Pet $pet;
```

**On a `ref` parameter directly** — when the `ref` property only accepts strings but you want type-safety or IDE completion for the reference target:

```php
#[OA\Response(ref: new OA\Schema\Ref(ref: '#/components/responses/NotFound'), response: 404)]
```

When used on `ref` directly, the `Refs` augmenter unwraps it to the plain string value before further resolution.
