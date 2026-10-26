## Specs

Collection of code/annotation examples and their corresponding OpenAPI specs generated using swagger-php.

* [api](specs/api) - Basic API
* [misc](specs/misc)
* [nesting](specs/nesting) - Nested schemas and class hierarchies
* [petstore](specs/petstore) - Classic petstore
* [polymorphism](specs/polymorphism) - Using `@OA\Discriminator`
* [using interfaces](specs/using-interfaces)
* [using-links](specs/using-links)
* [using refs](specs/using-refs)
* [using traits](specs/using-traits)
* [webhooks](specs/webhooks) - Using `@OA\Webhook`


## Custom augmenters

[Augmenters](../src/Augmenter) enrich the specification in spec and hybrid mode. Writing a custom augmenter is the way to extend that pipeline.

An augmenter implements `PipeInterface`: `group()` names the phase it runs in, and `__invoke()` receives the `Specification`:

```php
<?php
...
use OpenApi\Augmenter\Group;
use OpenApi\Utils\PipeInterface;
...

class MyCustomAugmenter implements PipeInterface
{
    public function group(): string|\BackedEnum
    {
        return Group::Augment;
    }

    public function __invoke(mixed $payload): mixed
    {
        // custom augmenting

        return $payload;
    }
}
```

It is added with `Builder::withAugmenters()`. Each example has a `scan.php` doing that, and the yaml it generates.

* [schema-query-parameter augmenter](augmenters/schema-query-parameter)

  An augmenter that takes a vendor extension naming a class, and adds each property of that class's schema as a
  query parameter to the [operation](augmenters/schema-query-parameter/SchemaQueryParameter.php).

* [sort-components augmenter](augmenters/sort-components)

  An augmenter that sorts schema components, so they appear in alphabetical order.


## Custom processors

> [!WARNING]
> Processors are part of the classic pipeline, which is deprecated in 7.0 and removed in 8.0. The augmenter examples above do the same in spec and hybrid mode.

[Processors](../src/Processors) implement the various steps involved in converting the annotations collected into an OpenAPI spec.

Writing a custom processor is the way to extend the classic pipeline.

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
