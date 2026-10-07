# Migrating to spec attributes 🧪

Moving a codebase from `OpenApi\Attributes` to `OpenApi\Spec` changes class names, argument
names and, in places, the shape of the attributes. The [differences from classic
attributes](/guide/spec-attributes#differences-from-classic-attributes) list them. swagger-php
ships a [Rector](https://getrector.com) set that makes those changes.

Annotations in docblocks are out of scope: the set reads PHP attributes only.

## Before you start

Generate your document in classic mode and keep it: the migration is done when spec mode
generates the same document from the migrated code.

Spec mode defaults to OpenAPI 3.1.0 and classic to 3.0.0, so pass the version explicitly to
both: `--version` on the command line, `Builder::setVersion()` in code.

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
- lifts `info:`, `servers:`, `tags:` and `externalDocs:` off a root `OpenApi` attribute into
  sibling attributes, and renames its `openapi:` to `version:`
- wraps a bare `enum: Suit::class` into `enum: [Suit::class]`
- turns a class-string `type: Pet::class` into `ref: Pet::class`
- renames the classes, `OpenApi\Attributes\Get` to `OpenApi\Spec\Operation\Get` and so on
- moves schema keywords off `Property`, `JsonContent` and `XmlContent` into a nested
  `schema: new Schema(...)`, since the spec classes no longer accept them
- imports the spec namespace as `use OpenApi\Spec as OAS;`

Formatting is left to your code style tooling.

## What it leaves for you

### A header beside several responses

Classic resolves a `#[Header]` stacked beside more than one `#[Response]` by copying it into
every one of them:

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

Spec mode reports `Ambiguous merge` instead and skips the class. Which response the header
belongs to is not something the code says, so the set does not guess. Nest it in the response
it belongs to, or give it a `component:` and reference it from each. See
[Components](/guide/spec-attributes#components).

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

Spec mode reports `Ambiguous merge` for the first `Response` and skips the class, which drops
both operations from the document. Give each operation its own `responses:`:

```php
#[OA\Operation\Get(path: '/pets/{id}', operationId: 'getPet', responses: [
    new OA\Response(response: 200, description: 'The pet'),
    new OA\Response(response: 404, description: 'No such pet'),
])]
#[OA\Operation\Post(path: '/pets/{id}', operationId: 'postPet', responses: [
    new OA\Response(response: 200, description: 'The pet'),
    new OA\Response(response: 404, description: 'No such pet'),
])]
public function pet(): void
```

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
- A `Property`, `JsonContent` or `XmlContent` that already has its own `schema:` beside other
  schema keywords is left whole, and fails at generation time with `Unknown named parameter`.
  Move the keywords into the nested schema by hand.

### The component keys

Spec attributes name a reusable component with `component:`. The classic spellings, `schema:`,
`parameter:`, `request:` and `securityScheme:`, still work on the spec attributes
until 8.0, generate the same document, and are left alone by the set. Each use triggers a PHP
deprecation, which appears wherever your application reports deprecations.

## Generator code

The set rewrites attributes. Code that drives the generator is yours to move:

- **The mode.** `Builder::setMode(Mode::SPEC)`, or `--mode spec` on the command line.
- **Custom processors.** Spec mode has no processor pipeline. A processor becomes an
  [augmenter](/guide/extension-points#augmenters), registered with `Builder::withAugmenters()`.
  It works on the `Specification` and spec attributes rather than on an `Analysis` of
  annotations, so it is a rewrite rather than a rename. Hybrid mode still runs processors added
  through `Builder::withGenerator()`, but
  [only over the annotations as scanned](/guide/extension-points#the-classic-escape-hatch).
- **Configuration keys.** Keys are named after the processor or augmenter they configure, so
  they change with the pipeline: classic's `expandEnums.enumNames` is spec's
  `enums.enumNames`. `openapi --mode <mode> -D <path>` prints every key a mode accepts.
- **Unreferenced components.** Spec mode removes components nothing references. Classic's
  equivalent, `cleanUnusedComponents.enabled`, is off by default. A component declared for use
  outside any path, such as a schema your client code is generated from, needs
  `cleanup.enabled` set to `false` to survive.

## Comparing the documents

Generate in spec mode at the version you pinned and compare with the classic document. Some
differences are expected and harmless. [Other differences](/guide/spec-attributes#other-differences)
lists them. Anything else is either a construct from the list above, or worth an
[issue](https://github.com/zircote/swagger-php/issues).
