# Migrating to Swagger-PHP v3.x

Swagger-PHP 3.x generates a openapi.json file that follows the OpenAPI Version 3.0.0 Specification.

If you need to output the older 2.x specification use swagger-php 2.x

## Changed annotations

### @SWG\Swagger() is renamed to @SWG\OpenApi()

### @SWG\Path() is renamed to @SWG\PathItem()
The spefication uses the term "Path Item Object", updated the annotation to reflect that.


### @SWG\Definition() is removed
Use @SWG\Schema() instead of @SWG\Definition() and update the references from "#/definitons/something" to "#/components/schemas/something".


