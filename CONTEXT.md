# swagger-php

A PHP library that generates OpenAPI specification documents from PHP source code
by scanning annotations (attributes and docblocks) and processing them into a
complete spec.

## Language

### Core Concepts

**Annotation**:
An OpenAPI specification element declared as a PHP 8+ attribute or legacy docblock comment on a class, method, or property.
_Avoid_: Attribute (too narrow), decorator, metadata

**Analysis**:
The aggregate result of scanning source code — contains all discovered annotations and structural definitions, before processing.
_Avoid_: Result, scan output

**Context**:
Nested metadata describing where an annotation was found in the source hierarchy (file, namespace, class, method, property).
_Avoid_: Location, position

**Generator**:
The orchestrator that coordinates scanning, processing, and output — it generates an OpenAPI spec from annotations, not code from a spec.
_Avoid_: Builder, compiler

**Processor**:
A single transformation step in an ordered pipeline that converts raw Analysis into a valid, complete OpenAPI specification.
_Avoid_: Handler, middleware, transformer

### Annotation Lifecycle

**Unmerged**:
An annotation that has been discovered but not yet incorporated into the target OpenAPI root object.
_Avoid_: Pending, orphaned

**Merge**:
Incorporating an annotation into its correct position within the OpenAPI object tree, guided by the nesting map.
_Avoid_: Combine, attach

**Augment**:
Filling in missing annotation fields with values inferred from code (e.g. deriving a schema type from a PHP type hint).
_Avoid_: Enrich, hydrate

**Expand**:
Resolving PHP inheritance (classes, interfaces, traits, enums) by copying parent annotations into child schemas.
_Avoid_: Inherit, flatten

### Structural Concepts

**Nesting**:
The declarative parent-child mapping (`$_nested`) that defines which annotation types can belong inside other annotation types — distinct from PHP class inheritance.
_Avoid_: Hierarchy (ambiguous with class hierarchy)

**Component**:
A reusable named definition stored in `#/components/` and referenced via `$ref` elsewhere in the spec.
_Avoid_: Shared schema, template

**Ref**:
A JSON Pointer (`$ref`) linking to another part of the spec, resolved by processors into `#/components/...` paths.
_Avoid_: Link (means something else in OpenAPI), pointer

### Scanning

**Analyser**:
Reflects on PHP source files to discover annotations and produce an Analysis.
_Avoid_: Scanner (too narrow — TokenScanner is a sub-component), parser

**AnnotationFactory**:
Creates annotation objects from discovered PHP attributes or docblock comments during analysis.
_Avoid_: Builder, constructor

## Relationships

- A **Generator** uses an **Analyser** to produce an **Analysis**
- An **Analysis** contains **Annotations**, each carrying a **Context**
- **Processors** run sequentially on an **Analysis**, first **merging** unmerged annotations, then **expanding** inheritance, then **augmenting** missing fields
- **Nesting** defines where an **Annotation** can be merged within the OpenAPI tree
- A **Component** is an **Annotation** that has been merged into `#/components/` and is reachable by **Ref**

## Example dialogue

> **Dev:** "I added a `@OA\Schema` on a class but it's not appearing in the output."
> **Domain expert:** "Is it still **unmerged**? Check that the **nesting** map allows it to be **merged** into Components, and that a **processor** hasn't filtered it out."

> **Dev:** "Why does the child class schema include the parent's properties?"
> **Domain expert:** "That's **expansion** — the ExpandClasses **processor** copies parent **annotations** into the child during the pipeline."

## Flagged ambiguities

- "generate" — resolved: reserve for the full end-to-end pipeline (`Generator::generate()`). Use **analyse** for the discovery phase and **serialize** for producing JSON/YAML output.
- "merge" — resolved: reserve for tree-placement (moving an annotation into its correct position in the OpenAPI object). Combining multiple annotations' fields into one (e.g. Properties into a Schema) is part of **augment**.
- "nested" — resolved: use **nesting map** when referring to the `$_nested` declaration. Use **enclosing** when talking about the physical source code structure (file, class, method) that Context tracks.