# Common techniques

## Annotation placement

You shouldn't place all annotations inside one big block, but scatter them throughout your codebase as close to the
relevant source code as appropriate.

`swagger-php` will scan your project and merge all meta-data into one` @OA\OpenApi` annotation.

::: warning
As of `swagger-php` v4 all annotations or attributes must be associated with
a structural element (`class`, `method`, `parameter` or `enum`)
:::

## Context awareness

`swagger-php` looks at the context of the annotation and will augment it with things like `property name`,
`data type` (doctype and native type hints) as well as a couple other things.

This means in a lot of cases it is not necessary to explicitly document all details.

**Example**

<codeblock id="context-awareness">
  <template v-slot:at>

<<< @/snippets/guide/common-techniques/context_awareness_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/common-techniques/context_awareness_an.php

  </template>
</codeblock>

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

<codeblock id="explicit-context">
  <template v-slot:at>

<<< @/snippets/guide/common-techniques/explicit_context_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/common-techniques/explicit_context_an.php

  </template>
</codeblock>

## Response media type

The `@OA\MediaType` is used to describe the content:

<codeblock id="response-media-type">
  <template v-slot:at>

<<< @/snippets/guide/common-techniques/response_media_type_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/common-techniques/response_media_type_an.php

  </template>
</codeblock>

But because most API requests and responses are JSON, the `@OA\JsonContent` allows you to simplify this by writing:

<codeblock id="response-json-content">
  <template v-slot:at>

<<< @/snippets/guide/common-techniques/response_json_content_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/common-techniques/response_json_content_an.php

  </template>
</codeblock>

During processing the `@OA\JsonContent` unfolds to `@OA\MediaType( mediaType="application/json", @OA\Schema(...)`
and will generate the same output.

## Using references - `$ref`

It's quite common that endpoints have some overlap in either their request or response data.
To keep things DRY (Don't Repeat Yourself) the specification allows reusing components using `$ref`'s

<codeblock id="using-references">
  <template v-slot:at>

```php
#[OA\Schema(
  schema: 'product_id',
  type: 'integer',
  format: 'int64',
  description: 'The unique identifier of a product in our catalog',
)]
```

  </template>
  <template v-slot:an>

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

  </template>
</codeblock>

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

<codeblock id="refer-to-$ref">
  <template v-slot:at>

```php
    #[OA\Property(ref: "#/components/schemas/product_id")]
    public $id;
```

  </template>
  <template v-slot:an>

```php
    /**
     * @OA\Property(ref="#/components/schemas/product_id")
     */
    public $id;
```

  </template>
</codeblock>

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

<codeblock id="custom-property">
  <template v-slot:at>

<<< @/snippets/guide/common-techniques/custom_property_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/common-techniques/custom_property_an.php

  </template>
</codeblock>

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

## Enums

[PHP 8.1 enums](https://www.php.net/manual/en/language.enumerations.basics.php) are supported in two main use cases.

### Enum cases as value

Enum cases can be used as value in an `enum` list just like a `string`, `integer` or any other primitive type.

**Basic enum:**

<codeblock id="enum-as-values">
  <template v-slot:at>

<<< @/snippets/guide/common-techniques/enum_as_values_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/common-techniques/enum_as_values_an.php

  </template>
</codeblock>

**Results in:**

```yaml
openapi: 3.0.0
components:
  schemas:
    Model:
      properties:
        someSuits:
          type: array
          enum:
            - Hearts
            - Diamonds
      type: object

```

**Backed enums**

If the enum is a backed enum, the case backing value is used instead of the name.

## Enum schemas

The simples way of using enums is to annotate them as `Schema`. This allows you to reference them like any other schema in your spec.

```php
use OpenApi\Attributes as OAT;

#[OAT\Schema()]
enum Colour
{
    case GREEN;
    case BLUE;
    case RED;
}

#[OAT\Schema()]
class Product
{
    #[OAT\Property()]
    public Colour $colour;
}
```

**Results in:**

```yaml
openapi: 3.0.0
components:
  schemas:
    Colour:
      type: string
      enum:
        - GREEN
        - BLUE
        - RED
    Product:
      properties:
        colour:
          $ref: '#/components/schemas/Colour'
      type: object
```

**Backed enums**

For backed enums there exist two rules that determine whether the name or backing value is used:
1. If no schema type is given, the enum name is used.
2. If a schema type is given, and it matches the backing type, the enum backing value is used.

**Using the name of a backed enum:**

```php
use OpenApi\Attributes as OAT;

#[OAT\Schema()]
enum Colour: int
{
    case GREEN = 1;
    case BLUE = 2;
    case RED = 3;
}

#[OAT\Schema()]
class Product
{
    #[OAT\Property()]
    public Colour $colour;
}
```

**Results in:**

```yaml
openapi: 3.0.0
components:
  schemas:
    Colour:
      type: string
      enum:
        - GREEN
        - BLUE
        - RED
    Product:
      properties:
        colour:
          $ref: '#/components/schemas/Colour'
      type: object
```

**Using the backing value:**

```php
use OpenApi\Attributes as OAT;

#[OAT\Schema(type: 'integer')]
enum Colour: int
{
    case GREEN = 1;
    case BLUE = 2;
    case RED = 3;
}

#[OAT\Schema()]
class Product
{
    #[OAT\Property()]
    public Colour $colour;
}
```

**Results in:**

```yaml
openapi: 3.0.0
components:
  schemas:
    Colour:
      type: integer
      enum:
        - 1
        - 2
        - 3
    Product:
      properties:
        colour:
          $ref: '#/components/schemas/Colour'
      type: object
```
