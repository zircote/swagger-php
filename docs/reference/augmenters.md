# Augmenter Reference

This page is generated automatically from the `swagger-php` sources.

For improvements head over to [GitHub](https://github.com/zircote/swagger-php) and create a PR ;)


Augmenters enrich the collected specification with inferred data before compilation.
They run in three groups — **resolve** (type inference, refs), **reduce** (filtering, cleanup),
and **augment** (docblocks, operation ids, tags) — and are listed below in execution order.

Augmenters are part of the spec-attributes pipeline (`--mode spec` or `--mode hybrid`).

## Augmenter Configuration

### Command line
The `-c` option allows to specify a name/value pair with the name consisting
of the augmenter name (starting lowercase) and option name separated by a dot (`.`).

```shell
> ./vendor/bin/openapi --mode spec -c operationId.hash=true // ...
> ./vendor/bin/openapi --mode spec -c pathFilter.tags[]=/pets/ -c pathFilter.tags[]=/store/ // ...
```

### Programmatically with PHP
Configuration can be set using the `Builder::withAugmenters()` method to access the pipeline
and configure individual augmenters via `Pipeline::get()`.

```php
(new Builder())
    ->withAugmenters(function ($pipeline) {
        $pipeline->get(Augmenter\OperationId::class)->setHash(true);
        $pipeline->get(Augmenter\PathFilter::class)->setTags(['/pets/', '/store/']);
    });
```

## Default Augmenters

### [ExpandHierarchy](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/ExpandHierarchy.php)
Expands PHP class hierarchy into OpenAPI composition (allOf).

For each schema backed by a class reflector, walks parents, traits, and interfaces:
- Ancestor with #[Schema] → adds $ref to allOf, stops walking up (parents only)
- Ancestor without #[Schema] → merges its own members into the current schema

After expansion, if a schema has both allOf and properties, the properties are
moved into a dedicated allOf entry (anonymous schema with type: object).

### [InferNames](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/InferNames.php)
Infers component names from PHP reflectors when not explicitly set.

Sets schema name from the class/interface/trait/enum short name,
and parameter component key from its name property.

### [Enums](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Enums.php)
Expands PHP enums into schema enum values.

For schemas attached to a PHP enum, determines schema name, type, and enum values.
Also resolves UnitEnum instances and enum class-strings in any schema's enum array.

Rules for name vs. value:
- Unit enums (not backed): always use case names, type becomes "string"
- Backed enums without explicit schema type: use case names, type becomes "string"
- Backed enums with schema type matching backing type (int→"integer", string→"string"):
  use backing values, type preserved
- Backed enums with schema type NOT matching backing type: use case names

#### Config settings
**enums.enumNames**
: <span style="font-family: monospace;">string</span>
<br>**default**
: <span style="font-family: monospace;">null</span>

&nbsp;&nbsp;&nbsp;&nbsp;If set, stores enum case names in a vendor extension with this key (e.g. <code>x-enum-varnames</code>).<br>

### [Type](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Type.php)
Infers schema type, format, nullable, items, etc. from PHP type declarations and docblocks.

Walks all properties and parameters in the specification and fills their schema
fields from the attached reflector's type information.

#### Config settings
**type.typeResolver**
: <span style="font-family: monospace;">OpenApi\Type\TypeResolver</span>
<br>**default**
: <span style="font-family: monospace;">OpenApi\Type\TypeResolver</span>

&nbsp;&nbsp;&nbsp;&nbsp;Override the type resolver used to infer schema types from PHP type declarations.<br>

### [Ref](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Ref.php)
Resolves FQCN-based $ref values to JSON Reference paths.

Builds a map of class names to their component paths and rewrites
any $ref that looks like a FQCN into the proper #/components/... path.

### [PathFilter](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/PathFilter.php)
Filters operations by tag and/or path patterns.

If no tags or paths filters are set, no filtering is performed.
All filter expressions must be valid regular expressions (with delimiters).

#### Config settings
**pathFilter.tags**
: <span style="font-family: monospace;">array</span>
<br>**default**
: <span style="font-family: monospace;">[]</span>

&nbsp;&nbsp;&nbsp;&nbsp;A list of regular expressions to match <code>tags</code> to include.<br>
**pathFilter.paths**
: <span style="font-family: monospace;">array</span>
<br>**default**
: <span style="font-family: monospace;">[]</span>

&nbsp;&nbsp;&nbsp;&nbsp;A list of regular expressions to match <code>paths</code> to include.<br>

### [CleanUnused](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/CleanUnused.php)
Removes unreferenced components from the specification.

Iterates multiple times to catch nested dependencies (a schema only
referenced by another unused schema should also be removed).

#### Config settings
**cleanUnused.enabled**
: <span style="font-family: monospace;">bool</span>
<br>**default**
: <span style="font-family: monospace;">true</span>

&nbsp;&nbsp;&nbsp;&nbsp;Enables/disables removal of unreferenced components.<br>

### [MediaType](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/MediaType.php)
Re-keys MediaType encoding lists by property name.

The assembler collects Encoding objects as a flat list via contains().
The compiler expects them as an associative array keyed by the property name
the encoding applies to.

### [Docblock](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Docblock.php)
Fills summary, description, and deprecated from PHP docblock comments.

Walks all attributes in the specification that have summary/description
properties and populates them from the reflector's docblock when not
explicitly set.

#### Config settings
**docblock.parser**
: <span style="font-family: monospace;">OpenApi\Utils\DocBlockParser</span>
<br>**default**
: <span style="font-family: monospace;">OpenApi\Utils\DocBlockParser</span>

&nbsp;&nbsp;&nbsp;&nbsp;Override the docblock parser used to extract summaries and descriptions.<br>

### [OperationId](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/OperationId.php)
Generates operationId for operations that don't have one explicitly set.

#### Config settings
**operationId.hash**
: <span style="font-family: monospace;">bool</span>
<br>**default**
: <span style="font-family: monospace;">true</span>

&nbsp;&nbsp;&nbsp;&nbsp;If set to <code>true</code> generate ids (md5) instead of clear text operation ids.<br>

### [Tag](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Tag.php)
Ensures all tags used on operations exist in the global tags list.

Adds missing Tag objects for any tag name referenced by operations.
Removes unused declared tags unless whitelisted.

#### Config settings
**tag.whitelist**
: <span style="font-family: monospace;">array</span>
<br>**default**
: <span style="font-family: monospace;">[]</span>

&nbsp;&nbsp;&nbsp;&nbsp;Whitelist tags to keep even if not used. Use '*' to keep all.<br>
**tag.withDescription**
: <span style="font-family: monospace;">bool</span>
<br>**default**
: <span style="font-family: monospace;">true</span>

&nbsp;&nbsp;&nbsp;&nbsp;Enables/disables generation of default tag descriptions.<br>
