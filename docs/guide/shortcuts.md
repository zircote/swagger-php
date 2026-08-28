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
attribute do not need an explicit `OA\Property` attribute — it will be added automatically.

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

## `OA\Parameter`

The `OA\Parameter` annotation requires specifying the `in` property to indicate where in the request the parameter is located.

Shortcut annotations are available for `OA\PathParameter`, `OA\QueryParameter`, `OA\CookieParameter` and `OA\HeaderParameter`.
