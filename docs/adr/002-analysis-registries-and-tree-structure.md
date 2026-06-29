# ADR-002: Analysis Registries and Tree Structure

## Status

Accepted

## Context

The OpenAPI specification is represented as a tree of annotation objects rooted at `OpenApi`. Processors need to both query annotations by type ("find all Schemas") and traverse structural relationships ("what refs does this subtree contain"). The system maintains two flat registries alongside the tree to support efficient queries.

## The Two Registries

### `$analysis->annotations` (SplObjectStorage)

A flat index of all annotations keyed by object identity, with Context as the attached value.

**Populated by:** `Analysis::addAnnotation()`, which recursively registers the annotation and all its nested children at the time of the call.

**Read by:**
- `getAnnotationsOfType()` — query all annotations of a given class (used by ~15 processors)
- Direct iteration in early-pipeline processors (`MergeIntoOpenApi`, `MergeIntoComponents`, `DocBlockDescriptions`, `CleanUnmerged`)
- Late-pipeline processors (`AugmentRefs`, `CleanUnusedComponents`)
- `Analysis::merged()` / `Analysis::unmerged()` / `Analysis::split()`

### `$context->annotations` (array)

A per-source-location list of annotations declared at that context.

**Populated by:** `Analysis::addAnnotation()`, which appends to the context's annotations array.

**Read by:**
- `getAnnotationForSource()` — finds the source-declared annotation for a FQDN. Critical for `ExpandClasses`, `ExpandTraits`, `ExpandInterfaces`, `AugmentSchemas`, `AugmentDiscriminators`, and type resolvers.

## The Annotation Tree

The annotation tree rooted at `$analysis->openapi` is the source of truth for what appears in the output. Annotations reachable from this root via non-blacklisted properties are serialized and validated.

## Registry Completeness

The registries are **complete** — every annotation in the tree is also in the registry. This is guaranteed by the combination of:

1. **Scanning phase:** The analyser calls `addAnnotation()` on all discovered annotations, which recursively registers their children.
2. **Processing phase:** Processors use `mergeAnnotations()` (or `addAnnotation()` directly) when creating or placing annotations, ensuring new annotations are always registered.

Since `addAnnotation()` is **idempotent** (early-returns if the annotation already exists), calling it on already-registered annotations is a safe no-op. This eliminates the need for processors to track whether an annotation is "new" or "already known."

## The `mergeAnnotations` Method

### Problem

Previously, processors that created new annotations needed two separate operations:
1. `$parent->merge([$annotation])` — place the annotation in the tree
2. `$analysis->addAnnotation($annotation, ...)` — register it in the index

This split was error-prone: some processors forgot step 2.

### Solution

`Analysis::mergeAnnotations($parent, $annotations, $ignore)` combines both:
1. Calls `$parent->merge($annotations, $ignore)` to place annotations in the tree
2. Calls `addAnnotation()` for each annotation to ensure registration

### When to use

| Situation | Method |
|-----------|--------|
| Processor merges annotations into a parent | `$analysis->mergeAnnotations($parent, [...])` |
| Processor creates an annotation and places it directly (not via merge) | `$analysis->addAnnotation($annotation, $context)` + direct assignment |
| Processor relocates an annotation (removes from old parent, adds to new) | `$analysis->removeAnnotation()` + direct assignment + `addAnnotation()` |

## Decision

1. Processors must use `$analysis->mergeAnnotations()` when merging annotations into the tree. This guarantees registry completeness without requiring callers to remember a separate registration step.

2. The registries are complete and can be relied upon for type-based queries and ref scanning. Iterating `$analysis->annotations` is sufficient to find all annotations, including those created by processors.

3. Processors that need type-based queries should use `getAnnotationsOfType()` — it's correct and efficient.

4. `AbstractAnnotation::merge()` should not be called directly by processors when `Analysis` is available. Direct `merge()` is acceptable only in contexts where annotations are already registered (e.g., trait methods operating on scan-time annotations without access to Analysis).