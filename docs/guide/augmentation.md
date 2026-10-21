# Augmentation

One benefit of using `swagger-php` is that the library will use a number of strategies to augment your
annotations.

This includes things like:

* the name of properties
* the description of elements
* the data type of properties

Based on the context of the annotation `swagger-php` will look at (in order of priority):

* the OpenApi annotation itself
* the docblock (if any)
* PHP native type hints (if any)

**Example**

<codeblock id="context-awareness">
  <template v-slot:at>

<<< @/snippets/guide/augmentation/context_awareness_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/augmentation/context_awareness_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/augmentation/context_awareness_spec.php

  </template>
</codeblock>

**Results in**
```yaml
openapi: 3.1.0
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

<<< @/snippets/guide/augmentation/explicit_context_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/augmentation/explicit_context_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/augmentation/explicit_context_spec.php

  </template>
</codeblock>

## Data types

In particular, the following schema elements may be augmented:

* `nullable`
* `items`
* `ref`
* `type`
* `format`
* `minimum` / `maximum`
* `const`
* `enum`
* `required`
* `default`

Data types that are not recognized and cannot be resolved to a class-like name (`class`, `interface`, `enum`, etc.) will be ignored.

A class implementing `DateTimeInterface`, such as `DateTimeImmutable` or a subclass of it, is a
`string` with format `date-time`.

Most of these schema elements will be resolved in isolation. For example, `nullable` may be enforced by the OpenApi annotation
while the actual data type may be resolved from the docblock (or type-hint).

### Which source wins

For each element, the first source that sets it wins:

| Element                                   | Sources, in order                                                                                |
| ----------------------------------------- | ------------------------------------------------------------------------------------------------ |
| `type`                                    | the attribute (`type`, `oneOf`, `allOf` or `anyOf`), the docblock, the type-hint                 |
| `format`, `minimum`, `maximum`, `items`   | the attribute, then the type the docblock or type-hint gives, unless the attribute sets `type`   |
| `nullable`                                | the attribute, then `true` if either the docblock or the type-hint is nullable, unless the attribute sets `type` |
| `ref`                                     | the attribute, then a class type, if that class is a schema component                           |

The docblock replaces the type-hint. It does not refine it: `@var string` on `public int $id` documents a `string`.
A docblock type that cannot be parsed is ignored, and the type-hint is used.

The docblock tag read depends on the element:

* a property: `@var`
* a promoted constructor property: the constructor's `@param`
* a method: `@return`

### Required properties and defaults

An object schema's `required` list and its properties' `default` come from the PHP declarations:

* a property is required when its PHP type is not nullable and it has no default. A property with
  no PHP type is never added.
* a property with a default gives that default, where it is a scalar, an enum case or an array of
  them. An enum case gives a backed case's value or a unit case's name, as the enum's schema lists
  them. `null` is not taken, and neither is a value the schema's `type` does not admit.

A parameter follows the same `required` rule, except that a path parameter is always required.
A promoted constructor property counts by its parameter's default. An explicit `required` list on
the schema is kept as written, and so is an explicit `default` on the property, `default: null`
included.

```php
#[OA\Schema]
class Pet
{
    #[OA\Property] public int $id;                 // required
    #[OA\Property] public ?string $nickname;       // not required: nullable
    #[OA\Property] public string $status = 'new';  // not required, default: new
}
```

::: info Mode difference
Only spec attributes are read. Classic annotations and attributes add neither, in `classic` or in
`hybrid`, and a classic parameter stays required by its type alone, whatever its default.
:::

## Summary and description

`summary` and `description` of certain annotation types (`OA\Operation`, `OA\Property`, `OA\Parameter` and `OA\Schema`) may be augmented
by extracting the summary and description of the docblock (if any).

## References

OpenApi specs support references to allow reuse of elements and reduce duplication. One of the most common use cases is
to set the `type` or `ref` property of a schema to a class name. The only requirement is that the class name must be
annotated as a `OA\Schema` itself.

## Tags

Tags are an OpenApi concept and may be added to your spec via the `Tag` annotation. For convenience, however, it is also
possible to create tags by just adding them to your HTTP operations (`Get`, `Post`, etc.).
All tags used that are not explicitly defined with a `Tag` annotation will be added to the spec.
