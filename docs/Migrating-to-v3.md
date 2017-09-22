# Migrating to Swagger-PHP v3.x

Swagger-PHP 3.x generates a openapi.json file that follows the OpenAPI Version 3.0.0 Specification.

If you need to output the older 2.x specification use swagger-php 2.x

## Changed annotations

## SWG is renamed to OAS
The namespace is renamed from SWG (Swagger) to OAS (OpenApiSpecification)
### @SWG\Swagger() is renamed to @OAS\OpenApi()

### @SWG\Path() is renamed to @OAS\PathItem()
The spefication uses the term "Path Item Object", updated the annotation to reflect that.

### @SWG\Definition() is removed
Use @OAS\Schema() instead of @OAS\Definition() and update the references from "#/definitons/something" to "#/components/schemas/something".


