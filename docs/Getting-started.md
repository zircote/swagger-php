# Getting started

## Installation

We recommend adding swagger-php to your project with [Composer](https://getcomposer.org))

```bash
composer require zircote/swagger-php
```

## Usage

Generate always-up-to-date documentation.

```php{3-5}
<?php
require("vendor/autoload.php");
$openapi = \OpenApi\scan('/path/to/project');
header('Content-Type: application/x-yaml');
echo $openapi->toYaml();
```

This will scan the php-files in the given folder(s), look for OpenApi annotations and output a json file.

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

## Write annotations

The goal of swagger-php is to generate a openapi.json using phpdoc annotations.

#### When you write:

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

#### swagger-php will generate:

```yaml
openapi: 3.0.0
info:
  title: "My First API"
  version: "0.1"
paths:
  /api/resource.json:
    get:
      responses:
        "200":
          description: "An example resource"
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

#### Results in:

```yaml
openapi: 3.0.0
paths:
  /products:
    get:
      summary: "list products"
      responses:
        "200":
          description: "A list with products"
        default:
          description: 'an "unexpected" error'
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

#### Results in:

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

#### As if you'd written:

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

### Shortcuts

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

But because most API requests and responses are JSON, the `@OA\JsonContent` allows you to write:

```php
/**
 * @OA\Response(
 *     response=200,
 *     description="successful operation",
 *     @OA\JsonContent(ref="#/components/schemas/User"),
 * )
 */
```

During processing the `@OA\JsonContent` unfolds to `@OA\MediaType( mediaType="application/json", @OA\Schema(`
and will generate the same output.

On a similar note, you generally don't have to write a `@OA\PathItem` because this annotation will be generated based on th path in operation `@OA\Get`, `@OA\Post`, etc.

## Reusing annotations ($ref)

It's common that multiple requests have some overlap in either the request or the response.
To keep thing DRY (Don't Repeat Yourself) the specification included referencing other parts of the json using `$ref`s

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

#### Results in:

```yaml
openapi: 3.0.0
components:
  schemas:
    product_id:
      description: "The unique identifier of a product in our catalog"
      type: integer
      format: int64
```

Which doesn't do anything by itself but now you can reference this piece by its path in the json `#/components/schemas/product_id`

```php
    /**
     * @OA\Property(ref="#/components/schemas/product_id")
     */
    public $id;
```

For more tips on refs, browse through the [using-refs example](https://github.com/zircote/swagger-php/tree/master/Examples/using-refs).

In swagger-php you can extend the components by altering specific fields using the `$` in-place of the `#`. But first try to use composition with [allOf](https://swagger.io/specification/#schemaComposition)

```php
    /**
     * @OA\Property(
     *   ref="$/components/schemas/product_id",
     *   format="int32"
     * )
     */
    public $id;
```

For extensions tips and examples, look at the [using-dynamic-refs example](https://github.com/zircote/swagger-php/tree/master/Examples/dynamic-reference).

## Vendor extensions

The specification allows for [custom properties](http://swagger.io/specification/#vendorExtensions) as long as they start with "x-" therefore all swagger-php annotations have an `x` property which will unfold into "x-" properties.

```php
openapi: 3.0.0
info:
  title: Example
  version: 1
  x-some-name: a-value
  x-another: 2
  x-complex-type:
    supported:
      - version: '1.0'
        level: baseapi
      - version: '2.1'
        level: fullapi
```

#### Results in:

```yaml
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

## OpenApi

To learn about what you can to generate and which options to use and how?  
Look at the [docs on swagger.io](https://swagger.io/docs/)

It has sections about:

- [Basic structure](https://swagger.io/docs/specification/basic-structure/)
- [Describing parameters](https://swagger.io/docs/specification/describing-parameters/)
- [Describing responses](https://swagger.io/docs/specification/describing-responses/)
- and [more](https://swagger.io/docs/specification/about/)

For more detailed information look at the [OpenApi Specification](http://swagger.io/specification/)
