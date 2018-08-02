# Guide

## Installation

We recommend adding swagger-php to your project with [Composer](https://getcomposer.org))

```bash
composer require zircote/swagger-php
```

## Usage

Generate always-up-to-date documentation.

```php
<?php
require("vendor/autoload.php");
$openapi = \OpenApi\scan('/path/to/project');
header('Content-Type: application/json');
echo $openapi;
```

## CLI

Instead of generating the documentation dynamicly we also provide a command line interface.
This writes the documentation to a static json file.

```bash
./vendor/bin/openapi --help
```

For cli usage from anywhere install swagger-php globally and add the `~/.composer/vendor/bin` directory to the PATH in your environment.

```bash
composer global require zircote/swagger-php
```

## Annotations

The goal of swagger-php is to generate a openapi.json using phpdoc annotations.

When you write:

```php
/**
 * @OA\Info(title="My First API", version="0.1")
 */

/**
 * @OA\Get(
 *     path="/api/resource.json",
 *     @OA\Response(response="200", description="An example resource")
 * )
 */
```

swagger-php will generate:

```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "My First API",
    "version": "0.1"
  },
  "paths": {
    "/api/resource.json": {
      "get": {
        "responses": {
          "200": {
            "description": "An example resource"
          }
        }
      }
    }
  }
}
```

### Using variables

You can use constants inside doctrine annotations.

```php
define("API_HOST", ($env === "production") ? "example.com" : "localhost");
```

```php
/**
 * @OA\Server(url=API_HOST)
 */
```

When you're using the CLI you'll need to include the php file with the constants using the `--bootstrap` options:

```bash
openapi --bootstrap constants.php
```

### Annotation placement

You shouldn't place all annotations inside one big @OA\OpenApi() annotation block, but scatter them throughout your codebase.
swagger-php will scan your project and merge all annotations into one @OA\OpenApi annotation.

The big benefit swagger-php provides is that the documentation lives close to the code implementing the api.

### Arrays and Objects

Doctrine annotation supports arrays, but uses `{` and `}` instead of `[` and `]`.

And although doctrine also supports objects, which also uses `{` and `}` and requires the property names to be surrounded with `"`.

::: warning DON'T WRITE

```php
/**
 * @OA\Info(
 *   title="My first API",
 *   version="1.0.0",
 *   contact={
 *     "email": "support@example.com"
 *   }
 * )
 */
```

:::

This "works" but most objects have an annotation with the same name as the property, such as `@OA\Contact` for `contact`:

::: tip WRITE

```php
/**
 * @OA\Info(
 *   title="My first API",
 *   version="1.0.0",
 *   @OA\Contact(
 *     email="support@example.com"
 *   )
 * )
 */
```

:::

This adds validation, so when you misspell a property or forget a required property it will trigger a php warning.
For example if you'd write `emial="support@example.com"` swagger-php whould generate a notice with `Unexpected field "emial" for @OA\Contact(), expecting "name", "email", ...`

Placing multiple annotations of the same type will result in an array of objects.
For objects, the key is define by the field with the name as the annotation: `response` in a `@OA\Response`, `property` in a `@OA\Property`, etc.

```php
/**
 * @OA\Get(
 *   path="/products",
 *   summary="list products",
 *   @OA\Response(
 *     response=200,
 *     description="A list with products"
 *   ),
 *   @OA\Response(
 *     response="default",
 *     description="an ""unexpected"" error"
 *   )
 * )
 */
```

Generates:

```json
{
  "openapi": "3.0",
  "paths": {
    "/products": {
      "get": {
        "summary": "list products",
        "responses": {
          "200": {
            "description": "A list with products"
          },
          "default": {
            "description": "an \"unexpected\" error"
          }
        }
      }
    }
  }
}
```

### Detects values based on context

swagger-php looks at the context of the comment which reduces duplication.

```php
/**
 * @OA\Schema()
 */
class Product {

    /**
     * The product name
     * @var string
     * @OA\Property()
     */
    public $name;
}
```

results in:

```json
{
  "openapi": "3.0",
  "components": {
    "schema": {
      "Product": {
        "properties": {
          "name": {
            "type": "string",
            "description": "The product name"
          }
        }
      }
    }
  }
}
```

As if you'd written:

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

## Don't Repeat Yourself

It's common that multiple requests have some overlap in either the request or the response.
The spec solves most of this by using `$ref`s

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

Results in:

```json
{
  "openapi": "3.0",
  "paths": {},
  "components": {
    "schemas": {
      "product_id": {
        "description": "The unique identifier of a product in our catalog",
        "type": "integer",
        "format": "int64"
      }
    }
  }
}
```

Which doesn't do anything by itself but now you can reference this piece by its path in the json `#/components/schemas/product_id`

```php
    /**
     * @OA\Property(ref="#/components/schemas/product_id")
     */
    public $id;
```

For more tips on refs, browse through the [using-refs example](https://github.com/zircote/swagger-php/tree/master/Examples/using-refs).

Alternatively, you can extend the definition altering specific fields using the `$` in-place of the `#`

```php
    /**
     * @OA\Property(
     *   ref="$/components/schemas/product_id",
     *   format="int32"
     * )
     */
    public $id;
```

For extensions tips and examples, browse through [using-dynamic-refs example](https://github.com/zircote/swagger-php/tree/master/Examples/dynamic-reference).

## Vendor extensions

The specification allows for [custom properties](http://swagger.io/specification/#vendorExtensions) as long as they start with "x-" therefor all swagger-php annotations have an `x` property which will unfold into "x-" properties.

```php
/**
 * @OA\Info(
 *   title="Example",
 *   version=1,
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

Results in:

```json
"info": {
  "title": "Example",
  "version": 1,
  "x-some-name": "a-value",
  "x-another": 2,
  "x-complex-type": {
    "supported": [{
      "version": "1.0",
      "level": "baseapi"
    }, {
      "version": "2.1",
      "level": "fullapi"
    }]
  }
},
```

The [Amazon API Gateway](http://docs.aws.amazon.com/apigateway/latest/developerguide/api-gateway-swagger-extensions.html) for example, makes use of these.

## More information

To see which output maps to which annotation checkout [swagger-explained](http://bfanger.github.io/swagger-explained/)
Which also contain snippets of the [swagger specification](http://swagger.io/specification/)
