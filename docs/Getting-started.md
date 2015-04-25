# Getting started

The goal of swagger-php is the generate a swagger.json using phpdoc annotations.

To output:

```json
{
   "swagger": "2.0",
   "schemes": ["http"],
   "host": "example.com",
   "basePath": "/api"
}
```

Write:

```php
/**
 * @SWG\Swagger(
 *   schemes={"http"},
 *   host="example.com",
 *   basePath="/api"
 * )
 */
```

Note that Doctrine annotation supports arrays, but uses the '{' and '}' instead of '[' and ']'.

And although doctrine also supports objects, but also uses '{' and '}' and require the properties to be surrounded with '"'.

DON'T write:

```php
/**
 * @SWG\Swagger(
 *   info={
 *     "title="My first swagger documented API",
 *     "version"="1.0.0"
 *   }
 * )
 */
```

Use the annotation with the same name as the property:

```php
/**
 * @SWG\Swagger(
 *   @SWG\Info(
 *     titel="My first swagger documented API",
 *     version="1.0.0"
 *   )
 * )
 */
```

This adds validation too so when you misspell a property or forget a required property it will trigger a php warning.  
For example the snippet above would generate the "Unexpected field "titel" for @SWG\Info(), expecting "title", ..." notice.


## Annotation placement

You shouldn't place all annotations inside one @SWG\Swagger() annotation, scattered annotations throughout your codebase.
swagger-php will scan your project and merge all annotations into one @SWG\Swagger annotation.

The big benefit swagger-php provides is that the documentation lives close the the code implementing the request.

### Arrays and Objects
Placing multiple annotation of the same type will result in an array or object.

```php
/**
 * @SWG\Get(
 *   path="/products",
 *   summary="list products",
 *   @SWG\Response(
 *     response=200,
 *     description="A list with products"
 *   ),
 *   @SWG\Response(
 *     response="default",
 *     description="an ""unexpected"" error"
 *   )
 * )
 */
```

Generates:

```json
{
  "swagger": "2.0",
  "paths": {
    "/products": {
      "get": {
        "summary": "list products",
        "responses": {
          "200": {
            "description": "A list with products"
          },
          "default": {
            "description": "unexpected error"
          }
        }
      }
    }
  },
  "definitions": []
}
```

### Swagger-PHP detects values based on context

swagger-php also looks at context of the comment which reduces duplication.

```php
/**
 * @SWG\Definition()
 */
class Product {

    /**
     * The product name
     * @var string
     * @SWG\Property()
     */
    public $name;
}
```

results in:

```json
{
  "swagger": "2.0",
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

## More information

To see which output maps to which annotation checkout [swagger-explained](http://bfanger.github.io/swagger-explained/)
Which also contain snippets of the [swagger specification](http://github.com/swagger-api/swagger-spec)
