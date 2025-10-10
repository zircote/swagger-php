# Shortcuts

To help keeping your annotations simple, there are a few shortcut annotations available in `swagger-php`.
Typically, these safe you from creating boilerplate nested `OA\Schema` annotations.

## `OA\MediaType`

`OA\MediaType` is used to describe the content of a response.

<codeblock id="response-media-type">
  <template v-slot:at>

<<< @/snippets/guide/shortcuts/response_media_type_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/shortcuts/response_media_type_an.php

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
</codeblock>

During processing the `OA\JsonContent` unwraps to `OA\MediaType(mediaType="application/json", OA\Schema(...)`
and will generate the same output.

The same applies to `OA\XmlContent`.

## `OA\Parameter`

The `OA\Parameter` annotation requires specifying the `in` property to indicate where in the request the parameter is located.

Shortcut annotations are available for `OA\PathParameter`, `OA\QueryParameter`, `OA\CookieParameter` and `OA\HeaderParameter`.
