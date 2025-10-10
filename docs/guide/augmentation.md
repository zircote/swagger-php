# Augmentation

One benefit of using `swagger-php` is that the library will use a number strategies to augment your
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

<<< @/snippets/guide/augmentation/explicit_context_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/augmentation/explicit_context_an.php

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

Data types that are not recognized and cannot be resolved to a class-like name (`class`, `interface`, `enum`, etc.) will be ignored.

Mots of these schema elements will be resolved in isolation. For example, `nullable` may be enfored by the OpenApi annotation
while the actual data type may be resolved from the docklock (or type-hint).

## Summary and description

`summary` and `description` of certain annotation types (`OA\Operation`, `OA\Property`, `OA\Parameter` and `OA\Schema`) may be augmented
by extracting the summary and description of the docblock (if any).

## References

OpenApi specs support references to allow reuse of elements and reduce duplication. One of the most common use cases is
to set the `type` or `ref` property of a schema to a class name. The only requirement is that the class name must be
annotated as a `OA\Schema` itself.

## Tags

Tags are an OpenApi concept and may be added to your spec via th `Tag` annotation. For convenience, however, it is also
possible to create tags by just adding them to your HTTP operations (`Get`, `Post`, etc.).
All tags used that are not explicitly defined with a `Tag` annotation will be added to the spec.
