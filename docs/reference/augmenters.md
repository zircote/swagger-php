# 🧪 Augmenter Reference

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

### [Inheritance](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Inheritance.php)

Expands PHP class hierarchy into OpenAPI composition (allOf).

For each schema backed by a class reflector, walks parents, traits, and interfaces:
- Ancestor with #[Schema] → adds $ref to allOf, stops walking up (parents only)
- Ancestor without #[Schema] → merges its own members into the current schema

After expansion, if a schema has both allOf and properties, the properties are
moved into a dedicated allOf entry (anonymous schema with type: object).

#### Config settings
- **inheritance.attributeFactory** : `OpenApi\Utils\AttributeFactory` · default: `OpenApi\Utils\AttributeFactory`  
  No details available.
- **inheritance.tokenScanner** : `OpenApi\Utils\TokenScanner` · default: `OpenApi\Utils\TokenScanner`  
  No details available.

### [Names](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Names.php)

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
- **enums.enumNames** : `string` · default: `null`  
  If set, stores enum case names in a vendor extension with this key (e.g. <code>x-enum-varnames</code>).

### [PathItems](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/PathItems.php)

Resolves PathItem prefixes, clones metadata to operations, and sets path-level output.

Walks the class hierarchy to compose path prefixes from ancestor PathItems,
prepends them to operation paths, clones tags/security/responses to operations
that don't declare their own, and marks PathItems that have spec-level output
(parameters, summary, description, servers) with their resolved path.

### [Types](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Types.php)

Infers schema type, format, nullable, items, etc. from PHP type declarations and docblocks.

Walks all properties and parameters in the specification and fills their schema
fields from the attached reflector's type information.

#### Config settings
- **types.typeResolver** : `OpenApi\Type\TypeResolver` · default: `OpenApi\Type\TypeResolver`  
  Override the type resolver used to infer schema types from PHP type declarations.

### [Refs](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Refs.php)

Resolves FQCN-based $ref values to JSON Reference paths.

Builds a map of class names to their component paths and rewrites
any $ref that looks like a FQCN into the proper #/components/... path.

### [PathFilter](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/PathFilter.php)

Filters operations by tag and/or path patterns.

If no tags or paths filters are set, no filtering is performed.
All filter expressions must be valid regular expressions (with delimiters).

#### Config settings
- **pathFilter.tags** : `array` · default: `[]`  
  A list of regular expressions to match <code>tags</code> to include.
- **pathFilter.paths** : `array` · default: `[]`  
  A list of regular expressions to match <code>paths</code> to include.

### [Cleanup](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Cleanup.php)

Removes unreferenced components from the specification.

Iterates multiple times to catch nested dependencies (a schema only
referenced by another unused schema should also be removed).

#### Config settings
- **cleanup.enabled** : `bool` · default: `true`  
  Enables/disables removal of unreferenced components.

### [MediaTypes](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/MediaTypes.php)

Re-keys MediaType encoding lists by property name.

The assembler collects Encoding objects as a flat list via contains().
The compiler expects them as an associative array keyed by the property name
the encoding applies to.

### [Docblocks](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Docblocks.php)

Fills summary, description, and deprecated from PHP docblock comments.

Walks all attributes in the specification that have summary/description
properties and populates them from the reflector's docblock when not
explicitly set.

#### Config settings
- **docblocks.parser** : `OpenApi\Utils\DocBlockParser` · default: `OpenApi\Utils\DocBlockParser`  
  Override the docblock parser used to extract summaries and descriptions.

### [OperationIds](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/OperationIds.php)

Generates operationId for operations that don't have one explicitly set.

#### Config settings
- **operationIds.hash** : `bool` · default: `true`  
  If set to <code>true</code> generate ids (md5) instead of clear text operation ids.

### [Tags](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Tags.php)

Ensures all tags used on operations exist in the global tags list.

Adds missing Tag objects for any tag name referenced by operations.
Removes unused declared tags unless whitelisted.

#### Config settings
- **tags.whitelist** : `array` · default: `[]`  
  Whitelist tags to keep even if not used. Use '*' to keep all.
- **tags.withDescription** : `bool` · default: `true`  
  Enables/disables generation of default tag descriptions.

### [EnumDescriptions](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/EnumDescriptions.php)

Generates a description for enum-based properties.

#### Config settings
- **enumDescriptions.enabled** : `bool` · default: `false`  
  Enables/disables generation of descriptions for enum based properties.
