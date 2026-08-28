# Inheritance Reference

This page documents the decision rules and mechanics of how PHP class hierarchy maps to OpenAPI composition in the spec pipeline. For usage examples see [Using Spec Attributes](../guide/spec-attributes.md#inheritance).

Two augmenters handle inheritance: [`Inheritance`](#schema-inheritance) (schema composition via `allOf`) and [`PathItems`](#pathitem-inheritance) (prefix composition and metadata cloning).

## Schema Inheritance

**Augmenter:** `OpenApi\Augmenter\Inheritance` · **Phase:** Resolve

The `Inheritance` augmenter walks the PHP class hierarchy for every schema that has a class reflector and expands it into OpenAPI `allOf` composition or inline property merging.

### Decision rules

For each schema, the augmenter processes three relationship types in order:

1. **Parents** — walks up `getParentClass()` chain
2. **Traits** — direct traits of the class, then traits of non-schema ancestors
3. **Interfaces** — direct interfaces of the class

For each ancestor encountered:

| Ancestor has `#[Schema]`? | Action                                           | Continue walking?       |
| ------------------------- | ------------------------------------------------ | ----------------------- |
| Yes                       | Add `$ref` to the schema's `allOf`               | **Stop** (parents only) |
| No                        | Merge ancestor's `#[OA\Property]` members inline | Continue                |

### Parent chain walk

```
class C extends B extends A
```

- If **B** has a schema → `C.allOf` gets `$ref: B`, walk stops
- If **B** has no schema but **A** does → B's properties merge into C, `C.allOf` gets `$ref: A`
- If neither has a schema → both B's and A's properties merge into C

The "stop at first schema ancestor" rule prevents redundant references — B's schema already composes A if needed.

### Trait handling

Traits are collected from two sources:

1. **Direct traits** of the schema's class
2. **Traits of non-schema ancestors** — the augmenter walks up the parent chain until it hits an ancestor with a schema and collects traits from each non-schema ancestor along the way

This ensures that when a non-schema parent uses a trait with `#[Schema]`, the composition is still captured.

### Interface handling

Only **direct** interfaces of the schema's class are processed. Unlike parents, there is no stop-on-first-schema rule — all direct interfaces with schemas contribute a `$ref`.

### Property merging

When an ancestor has no schema, its `#[OA\Property]` members are merged into the current schema. Deduplication is by property name — if the schema already declares a property with the same name, the ancestor's version is skipped. Merged properties are prepended to the schema's property list.

### allOf restructuring

After expansion, if a schema has both `allOf` entries and its own properties, the `Refs` augmenter (`mergeAllOf()`) moves the properties into a dedicated `allOf` entry — an anonymous schema with `type: object` — so the result is a pure `allOf` composition:

```yaml
User:
  allOf:
    - $ref: '#/components/schemas/BaseModel'
    - type: object
      properties:
        email:
          type: string
```

### Duplicate $ref deduplication

If you explicitly declare an `allOf` entry that matches one the augmenter would add (e.g. you extend a class and also manually reference it), the `Refs` augmenter (`dedupAllOfRefs()`) deduplicates — only one `$ref` survives.

## PathItem Inheritance

**Augmenter:** `OpenApi\Augmenter\PathItems` · **Phase:** Resolve

The `PathItems` augmenter resolves how `#[OA\PathItem]` attributes on controller classes compose via PHP inheritance to build operation paths and share metadata.

### Prefix composition

Each `PathItem` may declare a `prefix`. The augmenter composes prefixes by walking up the class hierarchy:

```php
#[OA\PathItem(prefix: '/api/v1')]
class BaseController {}

#[OA\PathItem(prefix: '/users')]
class UserController extends BaseController {}
```

Resolution walks from the class to root, collects prefixes in ancestor order (root first), and joins them:

```
/api/v1 + /users → /api/v1/users
```

The resolved prefix is prepended to each operation's `path`. An operation with `path: '/{id}'` in `UserController` becomes `/api/v1/users/{id}`.

### Governing PathItem

An operation's "governing" PathItem is found by walking up from the operation's declaring class until a class with `#[PathItem]` is found. Operations in a class without `#[PathItem]` can still inherit from an ancestor's PathItem.

### Metadata cloning

The augmenter clones metadata from PathItem (and its ancestors) to operations:

| Property    | Merge behavior                                                                                            |
| ----------- | --------------------------------------------------------------------------------------------------------- |
| `tags`      | Accumulated from all ancestors, deduplicated, appended to operation's existing tags                       |
| `security`  | Accumulated from all ancestors, deduplicated by scheme name                                               |
| `responses` | Accumulated from all ancestors, deduplicated by response code — operation's own responses take precedence |

All three accumulate additively up the hierarchy — every ancestor's PathItem contributes.

### Path-level parameters

PathItem `parameters` are inherited from ancestor PathItems and are emitted at the path level in the OpenAPI output. Deduplication is by `name + in` combination. The child's parameters take precedence over ancestors.

### Path-level output

A PathItem with `parameters`, `summary`, `description`, or `servers` produces path-level output in the OpenAPI document. The augmenter resolves which operation paths map to this PathItem and emits the path-level properties for each.
