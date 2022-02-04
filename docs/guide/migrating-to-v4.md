# Migrating to v4

## Overview
* As of PHP 8.1 annotations may be used as
  [PHP attributes](https://www.php.net/manual/en/language.attributes.overview.php) instead.
  That means all references to annotations in this document also apply to attributes.
* Annotations now **must be** associated  with either a class/trait/interface,
  method or property.
* A new annotation `PathParameter` was added for improved framework support.
* A new annotation `Attachable` was added to simplify custom processing.
  `Attachable` can be used to attach arbitrary data to any given annotation.
* Deprecated elements have been removed
  * `\Openapi\Analysis::processors()`
  * `\Openapi\Analyser::$whitelist`
  * `\Openapi\Analyser::$defaultImports`
  * `\Openapi\Logger`
* Legacy support is available via the previous `TokenAnalyser`
* Improvements to the `Generator` class

## Annotations as PHP attributes
While PHP attributes have been around since PHP 8.0 they were lacking the ability to be nested.
This changes with PHP 8.1 which allows to use `new` in initializers.

Swagger-php attributes also make use of named arguments, so attribute parameters can be (mostly) typed. 
There are some limitations to type hints which can only be resolved once support for PHP 7.x is dropped.

### Using annotations
```php

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *   version="1.0.0",
 *   title="My API",
 *   @OA\License(name="MIT"),
 *   @OA\Attachable()
 * )
 */
class OpenApiSpec
{
}
```
### Using attributes
```php

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'My API',
    attachables: [new OA\Attachable()]
)]
#[OA\License(name: 'MIT')]
class OpenApiSpec
{
}
```

## Optional nesting
One of the few differences between annotations and attributes visible in the above example is that the `OA\License` attribute
is not nested within `OA\Info`. Nesting of attributes is possible and required in certain cases however, **in cases where there
is no ambiguity attributes may be all written on the top level** and swagger-php will do the rest.

## Annotations must be associated with code
The (now legacy) way of parsing PHP files meant that docblocks could live in a file without a single line
of actual PHP code.

PHP Attributes cannot exist in isolation; they need code to be associated with and then are available 
via reflection on the associated code.
In order to allow to keep supporting annotations and the code simple it made sense to treat annotations and attributes
the same in this respect.

## The `PathParameter` annotation
As annotation this is just a short form for 
```php
   @OA\Parameter(in='body')
```

Things get more interesting when it comes to using it as attribute, though. In the context of
a controller you can now do something like
```php
class MyController
{
    #[OA\Get(path: '/products/{product_id}')]
    public function getProduct(
        #[OA\PathParameter] string $product_id)
    {
    }
```
Here it avoid having to duplicate details about the `$product_id` parameter and the simple use of the attribute
will pick up typehints automatically.

## The `Attachable` annotation
Technically these were added in version 3.3.0, however they become really useful only with version 4.

The attachable annotation is similar to the OpenApi vendor extension `x=`. The main difference are that
1. Attachables allow complex structures and strong typing
2. **Attachables are not added to the generated spec.**

Their main purpose is to make customizing swagger-php easier by allowing to add arbitrary data to any annotation.

One possible use case could be custom annotations. Classes extnding `Attachable` are allowed to limit 
the allowed parent annotations. This means it would be easy to create a new attribute to flag certain endpoints
as private and exclude them under certain conditions from the spec (via a custom processor).

## Removed deprecated elements
### `\Openapi\Analysis::processors()`
Processors have been moved into the `Generator` class incl. some new convenicen methods.
### `\Openapi\Analyser::$whitelist`
This has been replaced with the `Generator` `namespaces` property.
### `\Openapi\Analyser::$defaultImports`
This has been replaced with the `Generator` `aliases` property.
### `\Openapi\Logger`
This class has been removed completely. Instead, you may configure a [PSR-3 logger](https://www.php-fig.org/psr/psr-3/).

## Improvements to the `Generator` class
The removal of deprecated static config options means that the `Generator` class now is 
the main entry point into swagger-php when used programmatically.

To make the migration as simple as possible a new `Generator::withContext(callable)` has been added.
This allows to use parts of the library (an `Analyser` instance, for example) within the context of a `Generator` instance.

Example:
```php
$analyser = createMyAnalyser();

$analysis = (new Generator())
    ->addAlias('fo', 'My\\Attribute\\Namespace')
    ->addNamespace('Other\\Annotations\\')
    ->withContext(function (Generator $generator, Analysis $analysis, Context $context) use ($analyser) {
        $analyser->setGenerator($generator);
        $analysis = $analyser->fromFile('my_code.php', $context);
        $analysis->process($generator->getProcessors());

        return $analysis;
    });

```
