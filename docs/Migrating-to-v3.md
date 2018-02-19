# Migrating to Swagger-PHP v3.x

Swagger-PHP 3.x generates a openapi.json file that follows the OpenAPI Version 3.0.0 Specification.

If you need to output the older 2.x specification use swagger-php 2.x

## Changed annotations

## SWG is renamed to OAS
The namespace is renamed from SWG (Swagger) to OAS (OpenApiSpecification)
### @SWG\Swagger() is renamed to @OAS\OpenApi()

### @SWG\Path() is renamed to @OAS\PathItem()
The specification uses the term "Path Item Object", updated the annotation to reflect that.

### @SWG\Definition() is removed
Use @OAS\Schema() instead of @OAS\Definition() and update the references from "#/definitons/something" to "#/components/schemas/something".

### @SWG\Path is removed
Use @OAS\PathItem instead of @OAS\Path and update references.

### Consumes, produces field is removed from OpenAPI specification
Use @OAS\MediaType to set data format.

### Rename parameter references
Rename `#/parameters/{parameter_name}` to `#/components/parameters/{parameter_name}`

### Rename response references
Rename `#/responses/{response}` to `#/components/responses/{response}`

### More details about differences:

[A Visual Guide to What's New in Swagger 3.0](https://blog.readme.io/an-example-filled-guide-to-swagger-3-2/)
