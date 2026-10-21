# Shortcuts

Shortcuts save boilerplate: a typed subclass fills in the arguments that are always the same, such as `in: 'path'` or
`mediaType: 'application/json'`, and some forms save a nested `OA\Schema`.

| Shortcut | Replaces | Classic <Badge type="warning" text="deprecated" /> |
|---|---|---|
| [`OA\MediaType\Json`, `OA\MediaType\Xml`](#oa-mediatype-json-and-xml) | `OA\MediaType` with the media type set and a nested `OA\Schema` | `OA\JsonContent`, `OA\XmlContent` |
| [Implied `OA\Property`](#implied-oa-property) | `OA\Property` beside an `OA\Schema` on a class property | — |
| [Keyed `properties`](#keyed-properties) | a list of `OA\Property` | — |
| [`OA\Schema` subclasses](#oa-schema-subclasses) | `OA\Schema` for an array, a reference or additional properties | — |
| [`OA\Parameter\*`](#oa-parameter) | `OA\Parameter` with `in` | `OA\PathParameter`, `OA\QueryParameter`, `OA\HeaderParameter`, `OA\CookieParameter` |
| [`OA\Operation\*`](#oa-operation) | `OA\Operation` with `method` | `OA\Get`, `OA\Post` and so on |
| [`OA\Security\Requirement`](#oa-security-requirement) | a raw security array | — |
| [`OA\Security\Scheme\*`, `OA\Flow\*`](#oa-security-scheme-and-oa-flow) | `OA\Security\Scheme` with `type`, `OA\Flow` with `flow` | — |
| [`OA\Property\Encoded`](#oa-property-encoded) | an `OA\Encoding` listed apart from its property | — |

Each class in the [Spec Attribute Reference](/reference/spec-attributes) shows its shortcut next to the form it replaces.

## `OA\MediaType\Json` and `Xml` {#oa-mediatype-json-and-xml}

`OA\MediaType` describes the content of a response or request body.

<codeblock id="response-media-type">
  <template v-slot:at>

<<< @/snippets/guide/shortcuts/response_media_type_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/shortcuts/response_media_type_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/shortcuts/response_media_type_spec.php

  </template>
</codeblock>

`OA\MediaType\Json` sets `mediaType` to `application/json` and takes the schema keywords (`ref`, `type`, `items`,
`properties`, `required`) directly, so the `OA\Schema` is not needed:

<codeblock id="response-json-content">
  <template v-slot:at>

<<< @/snippets/guide/shortcuts/response_json_content_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/shortcuts/response_json_content_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/shortcuts/response_json_content_spec.php

  </template>
</codeblock>

Both generate the same output. `OA\MediaType\Xml` is the same for `application/xml`.

## Implied `OA\Property` {#implied-oa-property}

Class properties and promoted constructor parameters that have an `OA\Schema` attribute do not need an explicit
`OA\Property` attribute. It is added automatically.

**Verbose:**

<<< @/snippets/guide/shortcuts/optional_property_verbose_spec.php

**Shortcut:**

<<< @/snippets/guide/shortcuts/optional_property_short_spec.php

Both produce the same output. The implied `OA\Property` is inferred from the context (class property or promoted
constructor parameter with an `OA\Schema`).

This also applies to `OA\Schema` subclasses like `OA\Schema\Items`:

```php
// No explicit #[OA\Property] needed
#[OA\Schema\Items(ref: Tag::class)]
public array $tags;
```

The shortcut stops at methods, which supply no property name. `getTags()` is not `tags`, and the pipeline does not guess at
accessor prefixes. A getter needs the explicit `OA\Property`, carrying the name:

```php
#[OA\Property(property: 'tags')]
#[OA\Schema\Items(ref: Tag::class)]
public function getTags(): array
```

Without it the property is reported as missing its name and left out of the schema.

## Keyed `properties` {#keyed-properties}

Where there is no PHP class to declare the properties on, such as an array returned from a controller, `properties` can be
keyed by property name. The value is an `OA\Schema`, an `OA\Type` for a schema that sets nothing but the type, or an
`OA\Format` for a schema with that format and the type it implies (`OA\Format::DateTime` is a `string`).

**Verbose:**

<<< @/snippets/guide/shortcuts/keyed_properties_verbose_spec.php

**Shortcut:**

<<< @/snippets/guide/shortcuts/keyed_properties_short_spec.php

Both produce the same output. This works on `OA\Schema`, `OA\MediaType\Json` and `OA\MediaType\Xml`.

An `OA\Property` keeps its own `property`, so the two forms can be mixed in one array. Either way, the attribute stores a
list of `OA\Property`.

`OA\Type` can be used for the `type` of any schema, including a list such as `[OA\Type::Integer, OA\Type::Null]`. The attribute stores its
string value, so `OA\Type::String` and `'string'` produce the same document. The same goes for `OA\Format` and `format`. A
format that is not a case can still be passed as a string.

## `OA\Schema` subclasses {#oa-schema-subclasses}

- `OA\Schema\Items` is an array of the schema it describes: `new OA\Schema\Items(ref: Tag::class)` is
  `new OA\Schema(type: 'array', items: new OA\Schema(ref: Tag::class))`.
- `OA\Schema\Ref` is a reference, with an optional `title` and `description` that override the referenced schema's:
  `new OA\Schema\Ref(ref: Pet::class, description: 'The owner')`.
- `OA\Schema\AdditionalProperties` is an `OA\Schema` for the `additionalProperties` keyword, named for readability.

## `OA\Parameter\*` {#oa-parameter}

`OA\Parameter\Path`, `Query`, `Header` and `Cookie` set `in`. A path parameter is always required.

```php
// instead of
#[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]

// write
#[OA\Parameter\Path(name: 'id', schema: new OA\Schema(type: 'integer'))]
```

## `OA\Operation\*` {#oa-operation}

`OA\Operation\Get`, `Post`, `Put`, `Patch`, `Delete`, `Head`, `Options` and `Trace` set `method`.

```php
// instead of
#[OA\Operation(method: 'get', path: '/pets')]

// write
#[OA\Operation\Get(path: '/pets')]
```

## `OA\Security\Requirement` {#oa-security-requirement}

A security requirement names the schemes an operation needs. One `OA\Security\Requirement` is one entry in the
`security` list: the list means any of them, and `schemes` inside one means all of them.

```php
// instead of
#[OA\Operation\Get(path: '/pets', security: [['apiKey' => []]])]

// write
#[OA\Operation\Get(path: '/pets', security: [new OA\Security\Requirement(scheme: 'apiKey')])]

// both schemes at once
new OA\Security\Requirement(schemes: ['apiKey' => [], 'oauth' => ['read:pets']])
```

## `OA\Security\Scheme\*` and `OA\Flow\*` {#oa-security-scheme-and-oa-flow}

`OA\Security\Scheme\ApiKey`, `Http`, `OAuth2`, `OpenIdConnect` and `MutualTls` set `type`.
`OA\Flow\AuthorizationCode`, `ClientCredentials`, `Implicit` and `Password` set `flow`.

```php
// instead of
#[OA\Security\Scheme(component: 'bearerAuth', type: 'http', scheme: 'bearer')]

// write
#[OA\Security\Scheme\Http(component: 'bearerAuth', scheme: 'bearer')]

#[OA\Security\Scheme\OAuth2(component: 'oauth', flows: [
    new OA\Flow\ClientCredentials(tokenUrl: 'https://example.com/oauth/token', scopes: []),
])]
```

## `OA\Property\Encoded` {#oa-property-encoded}

A property that carries its own `OA\Encoding`, where the encoding would otherwise be listed on the media type, apart
from the property it belongs to.

```php
// instead of
new OA\MediaType\Json(
    properties: [new OA\Property(property: 'avatar', schema: new OA\Schema(type: 'object'))],
    encoding: [new OA\Encoding(encoding: 'avatar', contentType: 'image/png')],
)

// write
new OA\MediaType\Json(properties: [
    new OA\Property\Encoded(
        property: 'avatar',
        schema: new OA\Schema(type: 'object'),
        encoding: new OA\Encoding(contentType: 'image/png'),
    ),
])
```
