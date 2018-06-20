# Getting started

The goal of swagger-php is to generate a openapi.json using phpdoc annotations.

To output:

```json
{
   "openapi": "3.0",
   "host": "example.com"
}
```

Write:

```php
/**
 * @OAS\OpenApi(
 *   host="example.com",
 * )
 */
```

Note that Doctrine annotation supports arrays, but uses `{` and `}` instead of `[` and `]`.

And although doctrine also supports objects, but also uses `{` and `}` and requires the property names to be surrounded with `"`.

**DON'T** write:

```php
/**
 * @OAS\OpenApi(
 *   info={
 *     "title": "My first swagger-php documented API",
 *     "version": "1.0.0"
 *   }
 * )
 */
```

But use the annotation with the same name as the property, such as `@OAS\Info` for `info`:

```php
/**
 * @OAS\OpenApi(
 *   @OAS\Info(
 *     title="My first swagger-php documented API",
 *     version="1.0.0"
 *   )
 * )
 */
```

This adds validation, so when you misspell a property or forget a required property it will trigger a php warning.
For example if you'd write `titel="My first ...` swagger-php whould generate a notice with "Unexpected field "titel" for @OAS\Info(), expecting "title", ..."


## Using variables in annotations

You can use constants inside doctrine annotations.

```php
define("API_HOST", ($env === "production") ? "example.com" : "localhost");
```

```php
/**
 * @OAS\OpenApi(host=API_HOST)
 */
```

When you're using the CLI you'll need to include the php file with the constants using the `--bootstrap` options:
```
$ swagger --bootstrap constants.php
```


## Annotation placement

You shouldn't place all annotations inside one big @OAS\OpenApi() annotation block, but scatter them throughout your codebase.
swagger-php will scan your project and merge all annotations into one @OAS\OpenApi annotation.

The big benefit swagger-php provides is that the documentation lives close to the code implementing the api.

### Arrays and Objects

Placing multiple annotations of the same type will result in an array of objects.
For objects, the convention for properties, is to use the same field name as the annotation: `response` in a `@OAS\Response`, `property` in a `@OAS\Property`, etc.

```php
/**
 * @OAS\Get(
 *   path="/products",
 *   summary="list products",
 *   @OAS\Response(
 *     response=200,
 *     description="A list with products"
 *   ),
 *   @OAS\Response(
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

### Swagger-PHP detects values based on context

swagger-php looks at the context of the comment which reduces duplication.

```php
/**
 * @OAS\Schema()
 */
class Product {

    /**
     * The product name
     * @var string
     * @OAS\Property()
     */
    public $name;
}
```

results in:

```json
{
  "openapi": "3.0",
  "definitions": {
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
```

As if you'd written:

```php

    /**
     * The product name
     * @var string
     *
     * @OAS\Property(
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
     * @OAS\Schema(
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
     * @OAS\Property(ref="#/components/schemas/product_id")
     */
    public $id;
```

For more tips on refs, browse through the [using-refs example](https://github.com/zircote/swagger-php/tree/master/Examples/using-refs).

Alternatively, you can extend the definition altering specific fields using the `$` in-place of the `#`
```php
    /**
     * @SWG\Property(
     *   ref="$/definitions/product_id",
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
 * @OAS\Info(
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


## More information about Swagger

To see which output maps to which annotation checkout [swagger-explained](http://bfanger.github.io/swagger-explained/)
Which also contain snippets of the [swagger specification](http://swagger.io/specification/)
