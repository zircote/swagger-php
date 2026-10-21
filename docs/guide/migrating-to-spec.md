# Migrating to spec attributes

Moving a codebase from `OpenApi\Attributes` to `OpenApi\Spec` changes class names, argument
names and, in places, the shape of the attributes. The [differences from classic
attributes](/guide/spec-attributes#differences-from-classic-attributes) list them. swagger-php
ships a [Rector](https://getrector.com) set that makes those changes.

Annotations in docblocks are out of scope: the set reads PHP attributes only. Rector rules for
moving annotations to classic attributes first are collected in
[#1047](https://github.com/zircote/swagger-php/issues/1047).

## Before you start

Generate your document before migrating and keep it: the migration is done when the migrated
code generates the same document, apart from the
[differences spec mode is expected to show](/guide/spec-attributes#other-differences).

Classic mode defaults to OpenAPI 3.0.0 and the other modes to 3.1.0, so pass the version
explicitly to both: `--version` on the command line, `Builder::setVersion()` in code.

## Running the Rector set

```shell
composer require --dev rector/rector
./vendor/bin/rector process src \
    --config vendor/zircote/swagger-php/rector/set/classic-to-spec.php --dry-run
```

Drop `--dry-run` once the diff looks right.

Point it at every directory with code that uses an attribute, not only the directories your
generator scans. That includes classes that extend one, or construct one with `new`. A class
extending `OpenApi\Attributes\Response` that lives outside the scanned paths is migrated by
this run or not at all.

The set:

- names positional arguments by the classic constructor's parameters, including
  `parent::__construct()` in your own subclasses. The spec classes order their parameters
  differently, so a positional call would bind to the wrong ones after the rename
- renames `Encoding`'s `property:` to `encoding:`, and turns a `Property` with `encoding:` into a
  `Property\Encoded`, the spec property that takes one
- lifts `info:`, `servers:`, `tags:` and `externalDocs:` off a root `OpenApi` attribute into
  sibling attributes, and renames its `openapi:` to `version:`
- rewrites a parameter's `spaceDelimited:`, `pipeDelimited:` or `deepObject:` to the `style:`
  it stands for, `deepObject: true` to `style: 'deepObject'`. Classic writes them into the
  document as keys of their own, which OpenAPI does not define, so the migrated document gains
  the `style` and loses the stray key
- drops parameter arguments spec does not take where nothing is lost: an `in:` that repeats
  what `QueryParameter`, `PathParameter`, `HeaderParameter` or `CookieParameter` already says,
  `allowEmptyValue:` and `allowReserved:` outside query parameters and on `Header`, and any style
  on a header or cookie parameter. OpenAPI defines the first two for query parameters only, and
  allows one style each for headers and cookies. Classic writes them into the document as given;
  the migrated document leaves them out
- wraps a bare `enum: Suit::class` into `enum: [Suit::class]`
- turns a class-string `type: Pet::class` into `ref: Pet::class`
- rewrites the type names classic maps, such as `type: 'long'`, to the OpenAPI type and format
  they stand for, `type: 'integer', format: 'int64'`
- turns each item of `security:` into a `Security\Requirement`. A bare scheme name,
  `'bearerAuth'`, or an array, `['bearerAuth' => []]` or `['bearerAuth']`, becomes
  `new Security\Requirement(scheme: 'bearerAuth')`, with `scopes:` where it has any, and several
  schemes in one array become `schemes:`. Spec drops a string there, with a warning, and
  compiles an array only as a deprecated form. Items that are not literals, such as a constant,
  are left for you
- adds `#[Components]` to a class whose members declare keyed components, since spec takes a
  component on a member only in a holder. A class with a `Schema` of its own is left alone, and
  so is a keyed attribute that something beside it nests, such as a response beside an operation,
  and a response keyed by a status code, `response: 200`, which was meant to nest in one.
  A method carrying a framework's route attribute can be left out too, see
  [Routes from a framework](#routes-from-a-framework)
- renames the classes, `OpenApi\Attributes\Get` to `OpenApi\Spec\Operation\Get` and so on
- moves schema keywords off `Property`, `JsonContent` and `XmlContent` into a nested
  `schema: new Schema(...)`, since the spec classes no longer accept them. A `schema:` written
  as a reference, `'#/components/schemas/Pet'` or `Pet::class`, becomes that schema's `ref:`
- imports the spec namespace as `use OpenApi\Spec as OAS;`

Formatting is left to your code style tooling.

### Routes from a framework

Where a framework derives the operation from a route attribute, the attributes beside the route
decorate that operation:

```php
#[Route('/pets', methods: ['GET'])]
#[OA\Parameter(parameter: 'page', name: 'page', in: 'query')]
#[OA\Response(response: 200, description: 'The pets')]
public function list() {}
```

The response is keyed by a status code and does not count, but the set cannot tell the keyed
parameter from a component, and would make the class a holder. Name the route attributes in your
own `rector.php`, and a method carrying one of them does not count:

```php
use OpenApi\Rector\ComponentsHolderRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withSets([__DIR__ . '/vendor/zircote/swagger-php/rector/set/classic-to-spec.php'])
    ->withConfiguredRule(ComponentsHolderRector::class, [
        ComponentsHolderRector::ROUTE_ATTRIBUTES => [
            Symfony\Component\Routing\Attribute\Route::class,
            Symfony\Component\Routing\Annotation\Route::class,
        ],
    ]);
```

Then run `./vendor/bin/rector process src --dry-run` without `--config`.

To keep the rule out of whole directories instead, skip it there with Rector's own
`withSkip()`:

```php
    ->withSkip([ComponentsHolderRector::class => ['*/Controller/*']]);
```

## What it leaves for you

### A header beside several responses

Classic resolves a `#[Header]` stacked beside more than one `#[Response]` by copying it into
every one of them, and also lists it under `components/headers`:

```php
#[OA\Get(path: '/pets/{id}', operationId: 'getPet')]
#[OA\Response(response: 200, description: 'The pet')]
#[OA\Response(response: 404, description: 'No such pet')]
#[OA\Header(header: 'X-Request-Id', description: 'Correlates the request')]
public function get(): void
```

```yaml
responses:
  '200':
    description: 'The pet'
    headers:
      X-Request-Id:
        description: 'Correlates the request'
  '404':
    description: 'No such pet'
    headers:
      X-Request-Id:
        description: 'Correlates the request'
```

Spec mode reports `Ambiguous merge` instead and leaves the method out. Which response the header
belongs to is not something the code says, so the set does not guess. Set
`fan: OA\Fan::All` on the header to put it in every response, nest it in the response it
belongs to, or give it a `component:` and reference it from each. See
[One attribute for several targets](/guide/spec-attributes#one-attribute-for-several-targets)
and [Components](/guide/spec-attributes#components).

### Responses beside several operations

The same happens one level up. A method carrying more than one operation attribute, with
`#[Response]`s stacked beside them, gets every response copied into every operation in
classic:

```php
#[OA\Get(path: '/pets/{id}', operationId: 'getPet')]
#[OA\Post(path: '/pets/{id}', operationId: 'postPet')]
#[OA\Response(response: 200, description: 'The pet')]
#[OA\Response(response: 404, description: 'No such pet')]
public function pet(): void
```

Spec mode reports `Ambiguous merge` for the first `Response` and leaves the method out, which
drops both operations from the document. Where the operations differ only in their method, declare
one operation with `methods:`, and `{method}` in its `operationId`:

```php
#[OA\Operation(path: '/pets/{id}', operationId: '{method}Pet', methods: [OA\HttpMethod::Get, OA\HttpMethod::Post])]
#[OA\Response(response: 200, description: 'The pet')]
#[OA\Response(response: 404, description: 'No such pet')]
public function pet(): void
```

Otherwise set `fan: OA\Fan::All` on each shared `Response`, or give each operation its own
`responses:`. See [One operation for several methods](/guide/spec-attributes#one-operation-for-several-methods).

A response both operations share can be declared once with a `component:` and referenced as
`new OA\Response(response: 404, ref: '#/components/responses/NoSuchPet')` from each.

### Constructs with no one-to-one replacement

The set leaves these as they are:

- `OpenApi\Attributes\Query` is the HTTP `QUERY` operation, which is OpenAPI 3.2. In spec it is
  `#[OA\Operation(path: '/search', method: 'query')]`. `OpenApi\Attributes\Webhook` becomes a
  `webhook:` argument on an operation attribute, in place of `path:`.
- `OpenApi\Attributes\PathItem` is renamed, but the spec `PathItem` is a different concept and
  takes different arguments.
- A root `OpenApi` carrying `paths:`, `components:` or `webhooks:`, or children that are not
  written inline as `new` expressions, is not un-nested.
- Attributes on a constructor stay where they are, and spec ignores them with a warning. Move
  them to the class. See [Nothing is read from a constructor](/guide/spec-attributes#nothing-is-read-from-a-constructor).
- A `Property`, `JsonContent` or `XmlContent` that already has its own `schema:` beside other
  schema keywords is left whole, and fails at generation time with `Unknown named parameter`.
  Move the keywords into the nested schema by hand. The same holds for a `schema:` string that
  is neither a pointer nor a `::class`, and for one with a `ref:` beside it.

### Arguments spec does not take

These classic arguments have no spec counterpart and are left in place. Each throws
`Unknown named parameter` once the migrated attribute loads, so remove or replace it:

- `in:` on `QueryParameter`, `PathParameter`, `HeaderParameter` or `CookieParameter` that names
  another location, or is not a literal. Decide which the parameter is, and use that class.
- `collectionFormat:` on a schema, a property, items or content. It is OpenAPI 2.0; describe the
  serialization with `style:` and `explode:` on the parameter.
- `additionalItems:` and `dependencies:`. JSON Schema replaced them with `prefixItems:` beside
  `items:`, and with `dependentRequired:` and `dependentSchemas:`.
- `requestBody:` on `Get`, `Head`, `Options` and `Trace`. Spec defines no body for these
  methods, so remove it. See [No `requestBody` on `Get`, `Head`, `Options`,
  `Trace`](/guide/spec-attributes#no-requestbody-on-get-head-options-trace).
- `callbacks:` on `Components`. Declare callbacks on the operation.
- `variables:` on `ServerVariable` and `properties:` on `Attachable`, which spec has no use for.

`PathItem`'s `path:` and operation arguments, and a root `OpenApi`'s `paths:`, `components:` and
`webhooks:`, are covered [above](#constructs-with-no-one-to-one-replacement).

### The component keys

Spec attributes name a reusable component with `component:`. The classic spellings, `schema:`,
`parameter:`, `request:` and `securityScheme:`, still work on the spec attributes
until 8.0, generate the same document, and are left alone by the set. Each use triggers a PHP
deprecation, which appears wherever your application reports deprecations.

## Generator code

The set rewrites attributes. Code that drives the generator is yours to move:

- **The mode.** The default, hybrid, builds spec attributes. A `Builder::setMode()` call or
  `--mode` option can go.
- **Custom processors.** Spec mode has no processor pipeline. A processor becomes an
  [augmenter](/guide/extension-points#augmenters), registered with `Builder::withAugmenters()`.
  It works on the `Specification` and spec attributes rather than on an `Analysis` of
  annotations, so it is a rewrite rather than a rename. Hybrid mode still runs processors added
  through `Builder::withGenerator()`, but
  [only over the annotations as scanned](/guide/extension-points#the-classic-escape-hatch).
- **Configuration keys.** Keys are named after the processor or augmenter they configure, so
  they change with the pipeline: classic's `expandEnums.enumNames` is spec's
  `enums.enumNames`. `openapi -D <path>` prints every key the default accepts.
- **Unreferenced components.** Spec mode removes components nothing references. Classic's
  equivalent, `cleanUnusedComponents.enabled`, is off by default. A component declared for use
  outside any path, such as a schema your client code is generated from, needs
  `cleanup.enabled` set to `false` to survive.

## Comparing the documents

Generate from the migrated code at the version you pinned and compare with the classic document. Some
differences are expected and harmless. [Other differences](/guide/spec-attributes#other-differences)
lists them. Anything else is either a construct from the list above, or worth an
[issue](https://github.com/zircote/swagger-php/issues).
