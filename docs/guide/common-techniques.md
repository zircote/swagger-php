# Common techniques

## Annotation placement

You shouldn't place all annotations inside one big block, but scatter them throughout your codebase as close to the
relevant source code as appropriate.

`swagger-php` will scan your project and merge all meta-data into one` @OA\OpenApi` annotation.

::: warning
As of `swagger-php` v4 all annotations or attributes must be associated with code (`class`, `method`, `parameter` or `enum`)
:::

## Context awareness

`swagger-php` looks at the context of the annotation and will augment it with things like `property name`,
`data type` (doctype and native type hints) as well as a couple other things.

This means in a lot of cases it is not necessary to explicitly document all details.

**Example**
```php
<?php

/**
 * @OA\Schema()
 */
class Product {

    /**
     * The product name,
     * @var string
     * @OA\Property()
     */
    public $name;
}
```

**Results in**
```yaml
openapi: 3.0.0
components:
  schemas:
    Product:
      properties:
        name:
          description: "The product name"
          type: string
      type: object
```

**As if you'd written**

```php
    /**
     * The product name
     * @var string
     *
     * @OA\Property(
     *   property="name",
     *   type="string",
     *   description="The product name"
     * )
     */
    public $name;
```

## Response media type

The `@OA\MediaType` is used to describe the content:

```php
/**
 * @OA\Response(
 *     response=200,
 *     description="successful operation",
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(ref="#/components/schemas/User"),
 *     )
 * ),
 */
```

But because most API requests and responses are JSON, the `@OA\JsonContent` allows you to simplify this by writing:

```php
/**
 * @OA\Response(
 *     response=200,
 *     description="successful operation",
 *     @OA\JsonContent(ref="#/components/schemas/User"),
 * )
 */
```

During processing the `@OA\JsonContent` unfolds to `@OA\MediaType( mediaType="application/json", @OA\Schema(...)`
and will generate the same output.

## Using references - `$ref`

It's quite common that endpoints have some overlap in either their request or response data.
To keep things DRY (Don't Repeat Yourself) the specification allows reusing components using `$ref`'s

```php
/**
 * @OA\Schema(
 *   schema="product_id",
 *   type="integer",
 *   format="int64",
 *   description="The unique identifier of a product in our catalog"
 * )
 */
```

**Results in:**

```yaml
openapi: 3.0.0
components:
  schemas:
    product_id:
      description: "The unique identifier of a product in our catalog"
      type: integer
      format: int64
```

This doesn't do anything by itself, but now you can reference this fragment by its path in the document tree `#/components/schemas/product_id`

```php
    /**
     * @OA\Property(ref="#/components/schemas/product_id")
     */
    public $id;
```

::: info Examples
There are more uses cases on how to use refs in the [using-refs example](https://github.com/zircote/swagger-php/tree/master/Examples/using-refs).
:::

## Array parameters in query

Depending on [style-values](https://swagger.io/specification/#style-values) `@OA\Parameter(in="query", name="param", ...)`
might create urls like this: `path?param=123&param=abc` which do not work when reading the param values in PHP.

The solution is to change the name `param` into `param[]` which will create `path?param[]=123&param[]=abc`
and results in a PHP array.

## Vendor extensions

The specification allows for [custom properties](http://swagger.io/specification/#vendorExtensions)
as long as they start with "x-". Therefore, all swagger-php annotations have an `x` property which accepts an array (map)
and will unfold into "x-" properties.

```php
/**
 * @OA\Info(
 *   title="Example",
 *   version="1.0.0",
 *   x={
 *     "some-name": "a-value",
 *     "another": 2,
 *     "complex-type": {
 *       "supported":{
 *         {"version": "1.0", "level": "baseapi"},
 *         {"version": "2.1", "level": "fullapi"},
 *       }
 *     }
 *   }
 * )
 */
```

**Results in:**

```yaml
openapi: 3.0.0
info:
  title: Example
  version: 1
  x-some-name: a-value
  x-another: 2
  x-complex-type:
    supported:
      - version: "1.0"
        level: baseapi
      - version: "2.1"
        level: fullapi
```
