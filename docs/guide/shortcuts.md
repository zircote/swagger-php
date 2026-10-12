# Shortcuts

To help keeping your annotations simple, there are a few shortcut annotations available in `swagger-php`.
Typically, these save you from creating boilerplate nested `OA\Schema` annotations.

## `OA\MediaType`

`OA\MediaType` is used to describe the content of a response.

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

For `JSON` and `Xml` content, `swagger-php` provides shortcut
annotations to avoid having to specify the `mediaType` over and over again.

**Example using `OA\JsonContent`**

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

During processing the `OA\JsonContent` unwraps to `OA\MediaType(mediaType="application/json", schema: OA\Schema(...))`
and will generate the same output.

The same applies to `OA\XmlContent`.

## `OA\Property` (spec only)

When using the `OpenApi\Spec` namespace, class properties and promoted constructor parameters that have an `OA\Schema`
attribute do not need an explicit `OA\Property` attribute. It will be added automatically.

**Verbose:**

<<< @/snippets/guide/shortcuts/optional_property_verbose_spec.php

**Shortcut:**

<<< @/snippets/guide/shortcuts/optional_property_short_spec.php

Both produce the same output. The implicit `OA\Property` is inferred from the context (class property or promoted
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

## Keyed `properties` (spec only)

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

## `OA\Parameter`

The `OA\Parameter` annotation requires specifying the `in` property to indicate where in the request the parameter is located.

Shortcut annotations are available for `OA\PathParameter`, `OA\QueryParameter`, `OA\CookieParameter` and `OA\HeaderParameter`.
