# Annotations

::: tip Namespace
Using a namespace alias simplifies typing and improves readability.

All annotations are in the `OpenApi\Annotations` namespace.
:::

Since Annotations are technically PHP comments, adding `use OpenApi\Annotations as OA;` is strictly speaking not necessary.
However, doctrine will be quite specific about whether an alias is valid or not.

`swagger-php` will automatically register the `@OA` alias so all annotations can be used using the `@OA` shortcut without
any additional work.

## Doctrine
Annotations are PHP comments (docblocks) containing [doctrine style annotations](https://www.doctrine-project.org/projects/doctrine-annotations/en/latest/index.html).

::: info
All documentation related to doctrine applies to annotations only.
:::

**Example:**
```php
<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="My First API", version="0.1")
 */
class OpenApi {}

class MyController {

    /**
     * @OA\Get(
     *     path="/api/resource.json",
     *     @OA\Response(response="200", description="An example resource")
     * )
     */
    public function resource() {
        // ...
    }
}
```

## Arrays and Objects

Doctrine annotations support arrays, but use `{` and `}` instead of `[` and `]`.

Doctrine also supports objects, which also use `{` and `}` and require the property names to be surrounded with `"`.

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

This also adds validation, so when you misspell a property or forget a required property, it will trigger a PHP warning.

For example, if you write `emial="support@example.com"`, swagger-php would generate a notice with `Unexpected field "emial" for @OA\Contact(), expecting "name", "email", ...`

Placing multiple annotations of the same type will result in an array of objects.
For objects, the key is defined by the field with the same name as the annotation: `response` in a `@OA\Response`, `property` in a `@OA\Property`, etc.

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

**Results in**
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

## Constants

You can use constants inside doctrine annotations.

```php
define("API_HOST", ($env === "production") ? "example.com" : "localhost");
```

```php
/**
 * @OA\Server(url=API_HOST)
 */
```

:::tip
Using the CLI you might need to include the php file with the constants using the `--bootstrap` options.

```shell
> openapi --bootstrap constants.php
```
:::
