# Enums

`swagger-php` supports both basic and backed enums out of the box.

## Enum as `OA\Schema`

The simples way of using enums is to annotate them as `OA\Schema`. This allows you to reference them like any other schema in your spec.

<codeblock id="enum-as-schema">
  <template v-slot:at>

<<< @/snippets/guide/enums/enum_as_schema_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/enums/enum_as_schema_an.php

  </template>
</codeblock>

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

## Enum cases

Enum cases can be used as value in an `enum` list just like a `string`, `integer` or any other primitive type.

**Basic enum:**

<codeblock id="enum-as-values">
  <template v-slot:at>

<<< @/snippets/guide/enums/enum_as_values_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/enums/enum_as_values_an.php

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

:::tip Backed enums
If the enum is a backed enum, the case backing value is used instead of the name.
:::

## Backed enums

For backed enums there exist two rules that determine whether the name or backing value is used:
1. If **no schema type is given**, the enum name is used.
2. If a schema **type is given, and it matches the backing type**, the enum backing value is used.

### Default behaviour for backed enums

By default, the name of a backed enum case is used.

**Example:**

<codeblock id="backed-enum-names-as-schema">
  <template v-slot:at>

<<< @/snippets/guide/enums/backed_enum_names_as_schema_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/enums/backed_enum_names_as_schema_an.php

  </template>
</codeblock>

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

### Using the backing value of an enum

To force `swagger-php` to use the backing value of an enum, you need to set the schema type to match the enum backing type.

**Using the backing value (integer:**

<codeblock id="backed-enum-values-as-schema">
  <template v-slot:at>

<<< @/snippets/guide/enums/backed_enum_values_as_schema_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/enums/backed_enum_values_as_schema_an.php

  </template>
</codeblock>

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
