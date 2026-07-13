# 🧪 Using Spec Attributes

::: warning Beta
Spec attributes are feature-complete but still in beta. The API may evolve based on feedback before being promoted to the default mode in a future major version.
:::

Spec attributes are a new way to annotate your PHP code for OpenAPI generation. They live in the `OpenApi\Spec` namespace and are designed as simple, typed data containers — no serialization logic, no mutation, just clean PHP 8.1+ attributes.

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

In spec mode, `OA\Schema` is standalone — it doesn't inherit from anything. This means you can stack `#[OA\Property]` and `#[OA\Schema]` on the same target:

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

| Feature | Description |
|---|---|
| `prefix` | Composable path prefix, inherited via class hierarchy |
| `tags` | Cloned to all operations that don't declare their own |
| `security` | Cloned to all operations that don't declare their own |
| `responses` | Cloned to all operations (e.g. shared error responses) |
| `parameters` | Emitted as path-level parameters in the output |
| `summary` / `description` | Emitted at path level |
| `servers` | Emitted at path level |

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
Schemas, PathItems, SecuritySchemes, and named Responses/RequestBodies are root attributes — they can be declared directly on a class without a Components wrapper. Use Components only for types that can't stand alone (Parameter, Header, Link, Example).
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

| Classic (`OpenApi\Attributes`) | Spec (`OpenApi\Spec`) |
|---|---|
| `use OpenApi\Attributes as OA;` | `use OpenApi\Spec as OA;` |
| `#[OA\Get(path: '/pets')]` | `#[OA\Operation\Get(path: '/pets')]` |
| `#[OA\JsonContent(...)]` | `new OA\MediaType(mediaType: 'application/json', ...)` |
| `#[OA\PathParameter(...)]` | `#[OA\Parameter\Path(...)]` |
| Schema inherits from annotation base | Schema is standalone, stackable |
| Processors modify mutable annotation tree | Augmenters enrich immutable DTOs |
| Single serializer with version branches | Dedicated compiler per OpenAPI version |

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
