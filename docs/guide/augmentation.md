# Augmentation

One benefit of using `swagger-php` is that the library will use a number of strategies to augment your
attributes and annotations.

This includes things like:

* the name of properties
* the description of elements
* the data type of properties

Based on the context of the attribute or annotation `swagger-php` will look at (in order of priority):

* the attribute or annotation itself
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

Most of these schema elements will be resolved in isolation. For example, `nullable` may be enforced by the attribute or annotation
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

### Generic classes

A class that declares templates and is used with type arguments gets a schema for each set of
arguments. Each template is replaced by its argument in that schema, so a property typed `T`
documents a `Customer` in `Envelope<Customer>`, and an `int` in `Envelope<int>`:

```php
/**
 * @template T
 */
#[OA\Schema]
class Envelope
{
    /** @var T */
    #[OA\Property] public mixed $data;

    /** @var list<T> */
    #[OA\Property] public array $history;
}

#[OA\Schema]
class Holder
{
    /** @var Envelope<Customer> */
    #[OA\Property] public Envelope $customer;   // $ref: Envelope-of.Customer

    /** @var Envelope<int> */
    #[OA\Property] public Envelope $number;     // $ref: Envelope-of.int
}
```

The schema is a copy of the class's own, named after the class and its arguments. Each argument is
named as its own schema would be, and `_` separates the arguments, so `Pair<string, Customer>` is
`Pair-of.string_Customer`, and `Envelope<Envelope<Customer>>` is `Envelope-of.Envelope-of.Customer`.
Where the class's schema has a name of its own, the copies build on it:
`#[OA\Schema(component: 'Wrapper')]` gives `Wrapper-of.Customer`. So does an argument's: with
`component: 'BillingCustomer'` on `Billing\Customer`, `Envelope<Billing\Customer>` is
`Envelope-of.BillingCustomer`, apart from another `Customer`.

A member PHP declares `mixed` and types with a template is required in a copy unless its docblock
type takes `null`, as `T|null` does. A member without a PHP type is never required, in a copy or
not. A use with `mixed` only, or without arguments, references the class's plain schema. So does a
use with more or fewer arguments than the class has templates, with a warning. A class needs a
schema of its own to be copied.

Only the members a class declares itself read its templates as the arguments. A member it inherits
or takes from a trait keeps them unbound, with a warning: the arguments a class gives its parent
with `@extends`, or a trait with `@use`, are not read yet. A template a method declares in its own
docblock is the method's, as in PHPStan and Psalm, and stays unbound even where it shares a name
with the class's.

Rarer arguments keep their structure in the name, so no two uses share one:

| Use | Schema name |
|---|---|
| `Pair<Envelope<Customer>, int>` | `Pair-of.Envelope-of.Customer.-_int` |
| `Envelope<Pair<Customer, int>>` | `Envelope-of.Pair-of.Customer_int` |
| `Envelope<Customer\|int>` | `Envelope-of.Customer.or.int` |
| `Envelope<Customer&Countable>` | `Envelope-of.Customer.and.Countable` |
| `Envelope<list<Customer>>` | `Envelope-of.list-of.Customer` |
| `Envelope<array{id: int, note?: string}>` | `Envelope-of.shape-of.id.is.int_note.opt.string` |
| `Envelope<array{id: int, ...}>` | `Envelope-of.shape-of.id.is.int_etc` |

`.-` closes a nested list where another argument follows it. An array shape lists its members, with
`.is.` between a key and its type, `.opt.` for an optional key, and `etc` last where the shape takes
more keys. A type keyword with hyphens is written in camel case, so `Envelope<non-empty-string>` is
`Envelope-of.nonEmptyString`, and `class-string<Customer>` is `classString-of.Customer`. Inside an
argument, `-`, `.` and `_` are written `-h`, `-d` and `-u`, so a class named `Legacy_Customer`, or
one a [`QualifiedName` namer](/guide/extension-points#component-names) writes as `App.Customer`,
becomes `Legacy-uCustomer` and `App-dCustomer` there. Any other character a
name does not allow, such as the space in an array shape key `'a b'`, is written `-x` and its hex
code, `a-x20b`, so no two arguments share a name.

A ref can ask for the same schema, with the arguments as one type string:

```php
#[OA\Property(schema: new OA\Schema\Ref(ref: Envelope::class, qualifiers: ['of' => Customer::class]))]
```

A ref with other qualifiers besides `of`, such as `groups`, gets a copy of the schema described for
those qualifiers, so the arguments and the groups both apply.

How the copies are made is in [Spec pipeline internals](/dev/pipeline#how-type-arguments-flow).

OpenAPI 3.1 can also describe a generic schema once, with `$dynamicRef`. Few tools read it yet,
so swagger-php does not write it.

::: info Mode difference
Classic references the plain schema. In `hybrid`, classic attributes get a schema per set of
arguments too.
:::

### Property examples

::: info Mode difference
In the spec pipeline, a property's `@example` text is decoded as JSON when its value matches
the schema's type. For example, `@example false` on a boolean property produces the boolean
`false`. String schemas, unions containing `string`, and untyped schemas keep the text.
Invalid JSON and values of a different type also keep the text. An explicit schema `example`
or `examples`, including an empty list, takes precedence over the docblock. Property-backed
parameters and headers follow these example rules. Headers need an explicit schema type
for the example to be decoded, since their schema type is not inferred from the property.
:::

### Required properties and defaults

An object schema's `required` list and its properties' `default` come from the PHP declarations:

* a property is required when its PHP type is not nullable and it has no default. A property with
  no PHP type is never added.
* a property with a default gives that default, where it is a scalar, an enum case or an array of
  them. An enum case gives a backed case's value or a unit case's name, as the enum's schema lists
  them. `null` is not taken, and neither is a value the schema's `type` does not admit.

A parameter follows the same `required` rule, except that a path parameter is always required:
left unset, `required` becomes `true` whatever its PHP type or default, and an explicit
`required: false` gets a warning.
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
Only spec attributes are read. Classic attributes and docblock annotations add neither, in `classic` or in
`hybrid`, and a classic parameter stays required by its type alone, whatever its default. A path
parameter is the exception: in `hybrid` it is required too.
:::

## Summary and description

`summary` and `description` of certain attribute types (`OA\Operation`, `OA\Property`, `OA\Parameter`, `OA\Header` and `OA\Schema`) may be augmented
by extracting the summary and description of the docblock (if any).

::: info Mode difference
In the spec pipeline, parameters and headers carrying a property's reflector get their
description, `@example` and deprecation from its docblock. Promoted constructor parameters
also use the property's docblock. Property-backed nested schemas use the docblock of the
property they describe. Explicit values take precedence; `description: null` suppresses inference.
:::

## References

OpenApi specs support references to allow reuse of elements and reduce duplication. One of the most common use cases is
to set the `type` or `ref` property of a schema to a class name. The only requirement is that the class
is annotated with `OA\Schema` itself.

## Tags

Tags are an OpenApi concept and may be added to your spec with `OA\Tag`. For convenience, however, it is also
possible to create tags by just adding them to your HTTP operations (`Get`, `Post`, etc.).
All tags used that are not explicitly defined with `OA\Tag` will be added to the spec.
