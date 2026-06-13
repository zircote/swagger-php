# ADR-001: Context Keys and Annotation Lifecycle

## Status

Accepted

## Context

The `Context` class uses dynamic properties (via `__get` with prototypical inheritance) to carry metadata about where an annotation was found or created. Understanding these keys is essential for writing processors that create, move, or remove annotations.

## Context Keys

### Source location keys

Set by the analyser, inherited via the parent chain.

| Key | Type | Meaning |
|-----|------|---------|
| `filename` | `string` | Absolute path to the PHP file |
| `line` | `int` | Line number |
| `character` | `int` | Column offset |
| `namespace` | `string` | PHP namespace |
| `uses` | `array` | Import aliases (`['Alias' => 'Full\\Class']`) |
| `class` | `string` | Enclosing class name |
| `interface` | `string` | Enclosing interface name |
| `trait` | `string` | Enclosing trait name |
| `enum` | `string` | Enclosing enum name |
| `method` | `string` | Enclosing method name |
| `property` | `string` | Enclosing property name |
| `static` | `bool` | Whether the method/property is static |
| `extends` | `string\|array` | Parent class or interfaces extended |
| `implements` | `array` | Interfaces implemented |
| `comment` | `string` | The raw PHP DocComment |
| `reflector` | `\Reflector` | Reflection object for the element |
| `scanned` | `array` | Details from file scanner (ReflectionAnalyser) |

### Annotation relationship keys

| Key | Type | Meaning |
|-----|------|---------|
| `nested` | `AbstractAnnotation\|null` | The parent annotation this one is nested inside. `null` means explicitly not nested (top-level for merge purposes). Absent (not set) means the same as null but via inheritance — `is('nested')` returns `false`. |
| `annotations` | `list<AbstractAnnotation>` | All annotations registered on this context. Shared by annotations at the same source location. |

### Processing keys

| Key | Type | Meaning |
|-----|------|---------|
| `generated` | `bool` | The annotation/context was created by a processor, type resolver, or serializer (not from source scan). |
| `version` | `string` | The OpenAPI version in use (set on root context). |
| `logger` | `LoggerInterface` | PSR logger (guaranteed set when using Generator). |
| `other` | `list<AbstractAnnotation>` | Non-OpenApi annotations found at this location. |

## The `nested` Key

### How `is('nested')` works

`Context::is()` calls `property_exists()` — it checks whether the property is set directly on this context instance (not inherited from parent). This distinction drives processor behaviour:

- `is('nested') === true`: The property exists on this context. The annotation has an explicit nesting declaration.
- `is('nested') === false`: The property is not set. MergeIntoOpenApi/MergeIntoComponents treat this as "top-level, merge into root".

### Values

| Value | `is('nested')` | Meaning |
|-------|----------------|---------|
| `AbstractAnnotation` instance | `true` | This annotation is a child of that parent |
| `null` | `true` | Explicitly marked as having no parent (e.g. parameter-level attributes that should not be merged into root) |
| *(not set)* | `false` | Top-level — eligible for merge into OpenApi/Components |

### Where `nested` is set

1. **`AbstractAnnotation::__construct()`** (line 110): Creates a child context `['nested' => $this]` for annotations passed as constructor properties.

2. **`AbstractAnnotation::merge()`** (line 156): Same pattern for annotations merged into `_unmerged`.

3. **`AttributeAnnotationFactory`** (line 76): Sets `'nested' => null` for parameter-level attributes (`#[Property]`, `#[Parameter]`, `#[RequestBody]` on method parameters) that should not be merged into root.

4. **Processors** (e.g. MergeJsonContent): Should update `_context` when relocating an annotation to reflect its new parent.

### How processors use `nested`

- **MergeJsonContent/MergeXmlContent**: Read `$annotation->_context->nested` to find the parent (Response/RequestBody/Parameter) and check `instanceof`.
- **MergeIntoOpenApi/MergeIntoComponents**: Check `$annotation->_context->is('nested') === false` to find top-level annotations eligible for merging into root.

## Annotation Lifecycle

### Registration

`Analysis::addAnnotation($annotation, $context)` registers in two places:
1. `$this->annotations` (`SplObjectStorage`) — keyed by annotation, value is context
2. `$context->annotations[]` — array on the context object

### Removal

`Analysis::removeAnnotation($annotation)` removes from both:
1. The `SplObjectStorage`
2. The context's annotations array (context is retrieved from the SplObjectStorage before removal)

### When processors relocate annotations

When a processor transforms an annotation (e.g., JsonContent becomes a Schema inside a MediaType), it must:

1. **Update `_context`** — set a new context with `nested` pointing to the new parent, so tree-walking validation sees it in the correct location.
2. **Remove from parent's `_unmerged`** — so the old parent's validation doesn't warn about unexpected children.
3. **Remove from analysis registry** — via `$analysis->removeAnnotation()` so it's no longer discoverable via `getAnnotationsOfType()` and the old context's annotations array is cleaned up.

Example (from MergeJsonContent):
```php
// Create the new parent
$mediaType = new OA\MediaType(['schema' => $jsonContent, ...]);

// Update context to reflect new position in tree
$jsonContent->_context = new Context(['nested' => $mediaType, 'generated' => true], $mediaType->_context);

// Remove from old parent's _unmerged
array_splice($parent->_unmerged, $index, 1);

// Remove from analysis registry (cleans up SplObjectStorage + old context->annotations)
$analysis->removeAnnotation($jsonContent);
```

## Validation and Tree Walking

`Analysis::validate()` does NOT iterate the `SplObjectStorage`. It calls `collectAnnotations()` which walks the annotation tree starting from `$this->openapi`, following all non-blacklisted object properties recursively. This means:

- An annotation removed from the registry but still reachable via the tree **will** be validated.
- The `_context->nested` value determines whether validation considers the annotation correctly placed.
- Annotations with `$_parents = []` (like `JsonContent`) have no valid parent — if still reachable during tree walking, their context must point to a valid parent or they should be transformed into a type that has valid parents.

## Decision

Processors that consume/transform annotations must perform full cleanup:
1. Update `_context` to reflect the annotation's new position in the tree
2. Remove from old parent's `_unmerged`
3. Remove from analysis registry via `removeAnnotation()`

The `nested` context key should use `null` (not `false`) to indicate "explicitly no parent" — this matches the declared `@property OA\AbstractAnnotation|null` type.