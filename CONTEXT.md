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
The **classic** orchestrator that coordinates scanning, processing, and output — it generates an OpenAPI spec from annotations, not code from a spec.
_Avoid_: using "builder" or "compiler" as loose synonyms — both now name distinct classes in the spec pipeline (see below)

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
_Avoid_: constructor; and do not shorten it to "builder", which now names a class

### Spec Pipeline

The `spec` pipeline (`OpenApi\Spec`, `--mode spec`) has its own vocabulary. Terms here are
deliberately distinct from the classic ones above; do not use them interchangeably.

**Builder**:
The unified entry point. Selects a processing mode and orchestrates the run, returning a `Result`.
_Avoid_: Generator (that name is reserved for the classic orchestrator)

**Assembler**:
Collects spec attributes from PHP reflectors and resolves their nesting into a Specification.
_Avoid_: Analyser (classic), parser, scanner

**Specification**:
A flat, typed container holding the collected spec attributes, one bucket per root attribute type.
_Avoid_: Analysis (classic), tree, document

**Augmenter**:
A single pipe that enriches a Specification — the spec pipeline's counterpart to a classic Processor.
_Avoid_: Processor (reserved for the classic pipeline), middleware, handler

**Compiler**:
Transforms a Specification into a versioned OpenAPI document array. One per supported OpenAPI version.
_Avoid_: Serializer (classic output step), renderer

**Slot map**:
The `merge()` / `contained()` declarations by which an attribute names where it can nest. The slot names a property on the **target**, not on the declaring attribute.
_Avoid_: nesting map (that is the classic `$_nested`)

**Root attribute**:
An attribute that can live in the Specification without a parent container, and therefore has its own bucket.
_Avoid_: top-level (ambiguous with source-code position)

**Mode**:
Which pipeline a run uses: `classic`, `hybrid` or `spec`.
_Avoid_: driver, backend

## Relationships

- A **Generator** uses an **Analyser** to produce an **Analysis**
- An **Analysis** contains **Annotations**, each carrying a **Context**
- **Processors** run sequentially on an **Analysis**, first **merging** unmerged annotations, then **expanding** inheritance, then **augmenting** missing fields
- **Nesting** defines where an **Annotation** can be merged within the OpenAPI tree
- A **Component** is an **Annotation** that has been merged into `#/components/` and is reachable by **Ref**

In the spec pipeline:

- A **Builder** runs an **Assembler** to produce a **Specification**
- The **Assembler** resolves **slot maps** to decide what nests where; what survives are **root attributes**
- **Augmenters** run over the **Specification** in phases (resolve → reduce → augment)
- A **Compiler** turns the finished **Specification** into the output document

## Example dialogue

> **Dev:** "I added a `@OA\Schema` on a class but it's not appearing in the output."
> **Domain expert:** "Is it still **unmerged**? Check that the **nesting** map allows it to be **merged** into Components, and that a **processor** hasn't filtered it out."

> **Dev:** "Why does the child class schema include the parent's properties?"
> **Domain expert:** "That's **expansion** — the ExpandClasses **processor** copies parent **annotations** into the child during the pipeline."

## Flagged ambiguities

- "generate" — resolved: reserve for the full end-to-end pipeline (`Generator::generate()`). Use **analyse** for the discovery phase and **serialize** for producing JSON/YAML output.
- "merge" — resolved: reserve for tree-placement (moving an annotation into its correct position in the OpenAPI object). Combining multiple annotations' fields into one (e.g. Properties into a Schema) is part of **augment**.
- "augment" — **unresolved**. Classic uses it for filling in missing annotation fields (above). The spec pipeline also uses it as the name of the third augmenter phase (`Group::Augment`), which is narrower — `Types` and `Refs` do classic-style "augmenting" but run in the *resolve* phase. Say "the augment phase" when you mean the phase.
- "nested" — resolved: use **nesting map** when referring to the `$_nested` declaration. Use **enclosing** when talking about the physical source code structure (file, class, method) that Context tracks.