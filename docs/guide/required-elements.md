# Required elements

The OpenAPI specification defines a minimum set of information for a valid document.

For the most part that consists of some general information about the API like `name`, `version`
and at least one endpoint.

The endpoint, in turn, needs to have a path and at least one response.

## Minimum required annotations

With the above in mind a minimal API with a single endpoint could look like this

<codeblock id="minimal">
  <template v-slot:an>

<<< @/snippets/minimal_api_annotations.php

</template>
  <template v-slot:at>

<<< @/snippets/minimal_api_attributes.php

  </template>
</codeblock>

with the resulting OpenAPI document like this

<<< @/snippets/minimal_api.yaml

::: warning Code locations
Attributes and annotations can be added anywhere on declarations in code as defined by the PHP docs. 
These are limited to the extent of what the PHP Reflection APIs supports.
:::

## Optional elements

Looking at the generated document you will notice that there are some elements that `swagger-php` adds automatically
when they are missing.

For the most part those are `@OA\OpenApi`, `@OA\Components` and `@OA\PathItem`.
