# Minimum requirements

The OpenAPI specification defines a minimum set of information for a valid document.

For the most part that consists of some general information about the API like `name`, `version`
and at least one endpoint.

The endpoint, in turn, needs to have a path and at least one response.

## Minimum required annotations

With the above in mind, a minimal API with a single endpoint could look like this:

<codeblock id="minimal">
  <template v-slot:at>

<<< @/snippets/minimal_api_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/minimal_api_an.php

  </template>
</codeblock>

with the resulting OpenAPI document like this

<<< @/snippets/minimal_api.yaml

::: warning Where to add annotations?
Attributes and annotations can be added anywhere in your codebase as long as they are associated with structural elements
(classes, methods, properties, etc.).
Effectively, annotations can be added anywhere in your code where PHP reflection can find them.
:::

## Optional elements

Looking at the generated document, you will notice that there are some elements that `swagger-php` adds automatically
when they are missing.

For the most part those are `@OA\OpenApi`, `@OA\Components` and `@OA\PathItem`. These are defined by the OpenAPI specification
for grouping and as give the generated JSON/Yaml some more structure.
Unless there are specific reasons to add them manually, you should not need to add them yourself.

## Annotation placement

Your annotations should be placed next to your application code.
The idea is that you should be able to see your annotations right next to the code they describe.

This also has the advantage of making it easier to keep both in sync.

`swagger-php` will scan your project and merge all meta-data into one` @OA\OpenApi` annotation.

::: warning
As of `swagger-php` v4 all attributes and/or annotations must be associated with
a structural element (`class`, `method`, `parameter` or `enum`)
:::

