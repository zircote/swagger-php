# Processors

This page is generated automatically from the `swagger-php` sources.

For improvements head over to [GitHub](https://github.com/zircote/swagger-php) and create a PR ;)

## [DocBlockDescriptions](https://github.com/zircote/swagger-php/tree/master/src/Processors/DocBlockDescriptions.php)

Checks if the annotation has a summary and/or description property
and uses the text in the comment block (above the annotations) as summary and/or description.

Use `null`, for example: `@Annotation(description=null)`, if you don't want the annotation to have a description.
## [MergeIntoOpenApi](https://github.com/zircote/swagger-php/tree/master/src/Processors/MergeIntoOpenApi.php)

Merge all @OA\OpenApi annotations into one.
## [MergeIntoComponents](https://github.com/zircote/swagger-php/tree/master/src/Processors/MergeIntoComponents.php)

Merge reusable annotation into @OA\Schemas.
## [ExpandClasses](https://github.com/zircote/swagger-php/tree/master/src/Processors/ExpandClasses.php)

Iterate over the chain of ancestors of a schema and:
- if the ancestor has a schema
=> inherit from the ancestor if it has a schema (allOf) and stop.
- else
=> merge ancestor properties into the schema.
## [ExpandInterfaces](https://github.com/zircote/swagger-php/tree/master/src/Processors/ExpandInterfaces.php)

Look at all (direct) interfaces for a schema and:
- merge interfaces annotations/methods into the schema if the interface does not have a schema itself
- inherit from the interface if it has a schema (allOf).
## [ExpandTraits](https://github.com/zircote/swagger-php/tree/master/src/Processors/ExpandTraits.php)

Look at all (direct) traits for a schema and:
- merge trait annotations/methods/properties into the schema if the trait does not have a schema itself
- inherit from the trait if it has a schema (allOf).
## [ExpandEnums](https://github.com/zircote/swagger-php/tree/master/src/Processors/ExpandEnums.php)

Expands PHP enums.

Determines `schema`, `enum` and `type`.
## [AugmentSchemas](https://github.com/zircote/swagger-php/tree/master/src/Processors/AugmentSchemas.php)

Use the Schema context to extract useful information and inject that into the annotation.

Merges properties.
## [AugmentProperties](https://github.com/zircote/swagger-php/tree/master/src/Processors/AugmentProperties.php)

Use the property context to extract useful information and inject that into the annotation.
## [BuildPaths](https://github.com/zircote/swagger-php/tree/master/src/Processors/BuildPaths.php)

Build the openapi->paths using the detected `@OA\PathItem` and `@OA\Operation` (`@OA\Get`, `@OA\Post`, etc).
## [AugmentParameters](https://github.com/zircote/swagger-php/tree/master/src/Processors/AugmentParameters.php)


### Config settings
<dl>
  <dt><strong>augmentParameters.augmentOperationParameters</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>If set to <code>true</code> try to find operation parameter descriptions in the operation docblock.</p>  </dd>
</dl>


## [AugmentRefs](https://github.com/zircote/swagger-php/tree/master/src/Processors/AugmentRefs.php)

Update refs broken due to `allOf` augmenting.
## [MergeJsonContent](https://github.com/zircote/swagger-php/tree/master/src/Processors/MergeJsonContent.php)

Split JsonContent into Schema and MediaType.
## [MergeXmlContent](https://github.com/zircote/swagger-php/tree/master/src/Processors/MergeXmlContent.php)

Split XmlContent into Schema and MediaType.
## [OperationId](https://github.com/zircote/swagger-php/tree/master/src/Processors/OperationId.php)

Generate the OperationId based on the context of the OpenApi annotation.
### Config settings
<dl>
  <dt><strong>operationId.hash</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>If set to <code>true</code> generate ids (md5) instead of clear text operation ids.</p>  </dd>
</dl>


## [CleanUnmerged](https://github.com/zircote/swagger-php/tree/master/src/Processors/CleanUnmerged.php)


## [CleanUnusedComponents](https://github.com/zircote/swagger-php/tree/master/src/Processors/CleanUnusedComponents.php)


