## Specs

Collection of code/annotation examples and their corresponding OpenAPI specs generated using swagger-php.

* [api](specs/api) - Basic API
* [misc](specs/misc)
* [nesting](specs/nesting) - Nested schemas and class hierachies
* [petstore](specs/petstore) - Classic petstore
* [polymorphism](specs/polymorphism) - Using `@OA\Discriminator`
* [using interfaces](specs/using-interfaces)
* [using-links](specs/using-links)
* [using refs](specs/using-refs)
* [using traits](specs/using-traits)
* [webhooks](specs/webhooks) - Using `@OA\Webhooks`


## Custom processors

[Processors](../src/Processors) implement the various steps involved in converting the annotations collected into an OpenAPI spec.

Writing a custom processor is the recommended way to extend swagger-php in a clean way.

Processors are expected to implement the `__invoke()` method expecting the current `Analysis` object as single parameter:

```php
<?php
...
use OpenApi\Analysis;
...

class MyCustomProcessor
{
    public function __invoke(Analysis $analysis)
    {
        // custom processing
    }
}
```

* [schema-query-parameter processor](processors/schema-query-parameter)

  A processor that takes a vendor tag (expecting a schema `#ref`) and injects all properties of that given schema as
  query parameter to the [request definition](processors/schema-query-parameter/SchemaQueryParameter.php).

* [sort-components processor](processors/sort-components)

  A processor that sorts components, so they appear in alphabetical order.
