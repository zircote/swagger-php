# Processor Reference

This page is generated automatically from the `swagger-php` sources.

For improvements head over to [GitHub](https://github.com/zircote/swagger-php) and create a PR ;)


*Processors are listed in the default order of execution.*

## Processor Configuration
### Command line
The `-c` option allows to specify a name/value pair with the name consisting
of the processor name (starting lowercase) and  option name separated by a dot (`.`).

```shell
> ./vendor/bin/openapi -c operatinId.hash=true // ...
> ./vendor/bin/openapi -c pathFilter.tags[]=/pets/ -c pathFilter.tags[]=/store/ // ...
```

### Programmatically with PHP
Configuration can be set using the `Generator::setConfig()` method. Keys can either be the same
as on the command line or be broken down into nested arrays.

```php
(new Generator())
    ->setConfig([
        'operationId.hash' => true,
        'pathFilter' => [
            'tags' => [
                '/pets/',
                '/store/',
            ],
        ],
    ]);
```


## Default Processors
### [DocBlockDescriptions](https://github.com/zircote/swagger-php/tree/master/src/Processors/DocBlockDescriptions.php)

Checks if the annotation has a summary and/or description property
and uses the text in the comment block (above the annotations) as summary and/or description.

Use <code>null</code>, for example: <code>@Annotation(description=null)</code>, if you don't want the annotation to have a description.
### [MergeIntoOpenApi](https://github.com/zircote/swagger-php/tree/master/src/Processors/MergeIntoOpenApi.php)

Merge all <code>@OA\OpenApi</code> annotations into one.
#### Config settings
**mergeIntoOpenApi.mergeComponents**
: <span style="font-family: monospace;">bool</span>
<br>**default**
: <span style="font-family: monospace;">false</span>

&nbsp;&nbsp;&nbsp;&nbsp;If set to <code>true</code>, allow multiple `@OA\Components` annotations to be merged.<br>



### [MergeIntoComponents](https://github.com/zircote/swagger-php/tree/master/src/Processors/MergeIntoComponents.php)

Merge reusable annotation into <code>@OA\Schemas</code>.
### [ExpandClasses](https://github.com/zircote/swagger-php/tree/master/src/Processors/ExpandClasses.php)

Iterate over the chain of ancestors of a schema and:
- if the ancestor has a schema
  => inherit from the ancestor if it has a schema (allOf) and stop.
- else
  => merge ancestor properties into the schema.
### [ExpandInterfaces](https://github.com/zircote/swagger-php/tree/master/src/Processors/ExpandInterfaces.php)

Look at all (direct) interfaces for a schema and:
- merge interfaces annotations/methods into the schema if the interface does not have a schema itself
- inherit from the interface if it has a schema (allOf).
### [ExpandTraits](https://github.com/zircote/swagger-php/tree/master/src/Processors/ExpandTraits.php)

Look at all (direct) traits for a schema and:
- merge trait annotations/methods/properties into the schema if the trait does not have a schema itself
- inherit from the trait if it has a schema (allOf).
### [ExpandEnums](https://github.com/zircote/swagger-php/tree/master/src/Processors/ExpandEnums.php)

Expands PHP enums.

Determines <code>schema</code>, <code>enum</code> and <code>type</code>.
#### Config settings
**expandEnums.enumNames**
: <span style="font-family: monospace;">string</span>
<br>**default**
: <span style="font-family: monospace;">null</span>

&nbsp;&nbsp;&nbsp;&nbsp;Specifies the name of the extension variable where backed enum names will be stored.<br>
&nbsp;&nbsp;&nbsp;&nbsp;Set to <code>null</code> to avoid writing backed enum names.<br>
&nbsp;&nbsp;&nbsp;&nbsp;<br>
&nbsp;&nbsp;&nbsp;&nbsp;Example:<br>
&nbsp;&nbsp;&nbsp;&nbsp;<code>->setEnumNames('enumNames')</code> yields:<br>
```yaml
  x-enumNames:
    - NAME1
    - NAME2
```

**expandEnums.generator**
: <span style="font-family: monospace;">OpenApi\Generator</span>
<br>**default**
: <span style="font-family: monospace;">N/A</span>

&nbsp;&nbsp;&nbsp;&nbsp;No details available.<br>



### [AugmentSchemas](https://github.com/zircote/swagger-php/tree/master/src/Processors/AugmentSchemas.php)

Use the Schema context to extract useful information and inject that into the annotation.

Merges properties.
### [AugmentRequestBody](https://github.com/zircote/swagger-php/tree/master/src/Processors/AugmentRequestBody.php)

Use the RequestBody context to extract useful information and inject that into the annotation.
#### Config settings
**augmentRequestBody.generator**
: <span style="font-family: monospace;">OpenApi\Generator</span>
<br>**default**
: <span style="font-family: monospace;">N/A</span>

&nbsp;&nbsp;&nbsp;&nbsp;No details available.<br>



### [AugmentProperties](https://github.com/zircote/swagger-php/tree/master/src/Processors/AugmentProperties.php)

Use the property context to extract useful information and inject that into the annotation.
#### Config settings
**augmentProperties.generator**
: <span style="font-family: monospace;">OpenApi\Generator</span>
<br>**default**
: <span style="font-family: monospace;">N/A</span>

&nbsp;&nbsp;&nbsp;&nbsp;No details available.<br>



### [AugmentDiscriminators](https://github.com/zircote/swagger-php/tree/master/src/Processors/AugmentDiscriminators.php)

Use the property context to extract useful information and inject that into the annotation.
### [BuildPaths](https://github.com/zircote/swagger-php/tree/master/src/Processors/BuildPaths.php)

Build the openapi->paths using the detected <code>@OA\PathItem</code> and <code>@OA\Operation</code> (<code>@OA\Get</code>, etc).
### [AugmentParameters](https://github.com/zircote/swagger-php/tree/master/src/Processors/AugmentParameters.php)

Augments shared and operations parameters from docblock comments.
#### Config settings
**augmentParameters.augmentOperationParameters**
: <span style="font-family: monospace;">bool</span>
<br>**default**
: <span style="font-family: monospace;">true</span>

&nbsp;&nbsp;&nbsp;&nbsp;If set to <code>true</code> try to find operation parameter descriptions in the operation docblock.<br>

**augmentParameters.generator**
: <span style="font-family: monospace;">OpenApi\Generator</span>
<br>**default**
: <span style="font-family: monospace;">N/A</span>

&nbsp;&nbsp;&nbsp;&nbsp;No details available.<br>



### [AugmentRefs](https://github.com/zircote/swagger-php/tree/master/src/Processors/AugmentRefs.php)


### [MergeJsonContent](https://github.com/zircote/swagger-php/tree/master/src/Processors/MergeJsonContent.php)

Split JsonContent into Schema and MediaType.
### [MergeXmlContent](https://github.com/zircote/swagger-php/tree/master/src/Processors/MergeXmlContent.php)

Split XmlContent into Schema and MediaType.
### [AugmentMediaType](https://github.com/zircote/swagger-php/tree/master/src/Processors/AugmentMediaType.php)

Augment media type encodings.
### [OperationId](https://github.com/zircote/swagger-php/tree/master/src/Processors/OperationId.php)

Generate the OperationId based on the context of the OpenApi annotation.
#### Config settings
**operationId.hash**
: <span style="font-family: monospace;">bool</span>
<br>**default**
: <span style="font-family: monospace;">true</span>

&nbsp;&nbsp;&nbsp;&nbsp;If set to <code>true</code> generate ids (md5) instead of clear text operation ids.<br>



### [CleanUnmerged](https://github.com/zircote/swagger-php/tree/master/src/Processors/CleanUnmerged.php)


### [PathFilter](https://github.com/zircote/swagger-php/tree/master/src/Processors/PathFilter.php)

Allows to filter endpoints based on tags and/or path.

If no <code>tags</code> or <code>paths</code> filters are set, no filtering is performed.

All filter (regular) expressions must be enclosed within delimiter characters as they are used as-is.
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

**pathFilter.recurseCleanup**
: <span style="font-family: monospace;">bool</span>
<br>**default**
: <span style="font-family: monospace;">false</span>

&nbsp;&nbsp;&nbsp;&nbsp;Flag to do a recursive cleanup of unused paths and their nested annotations.<br>



### [CleanUnusedComponents](https://github.com/zircote/swagger-php/tree/master/src/Processors/CleanUnusedComponents.php)

Tracks the use of all <code>Components</code> and removed unused schemas.
#### Config settings
**cleanUnusedComponents.enabled**
: <span style="font-family: monospace;">bool</span>
<br>**default**
: <span style="font-family: monospace;">false</span>

&nbsp;&nbsp;&nbsp;&nbsp;Enables/disables the <code>CleanUnusedComponents</code> processor.<br>



### [AugmentTags](https://github.com/zircote/swagger-php/tree/master/src/Processors/AugmentTags.php)

Ensures that all tags used on operations also exist in the global <code>tags</code> list.
#### Config settings
**augmentTags.whitelist**
: <span style="font-family: monospace;">array</span>
<br>**default**
: <span style="font-family: monospace;">[]</span>

&nbsp;&nbsp;&nbsp;&nbsp;Whitelist tags to keep even if not used. <code>*</code> may be used to keep all unused.<br>



