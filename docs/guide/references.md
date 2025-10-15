# References

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
There are more uses cases on how to use refs in the [using-refs example](https://github.com/zircote/swagger-php/tree/master/examples/specs/using-refs).
:::
