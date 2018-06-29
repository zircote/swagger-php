# Migrating to Swagger-PHP v3.x

Swagger-PHP 3.x generates a openapi.json file that follows the OpenAPI Version 3.0.0 Specification.

If you need to output the older 2.x specification use OpenApi-php 2.x

## Changed annotations

## SWG is renamed to OA

The namespace is renamed from SWG (Swagger) to OA (OpenApi)

### @SWG\Swagger() is renamed to @OA\OpenApi()

### @SWG\Path() is renamed to @OA\PathItem()

The specification uses the term "Path Item Object", updated the annotation to reflect that.

### @SWG\Definition() is removed

Use @OA\Schema() instead of @OA\Definition() and update the references from "#/definitons/something" to "#/components/schemas/something".

### @SWG\Path is removed

Use @OA\PathItem instead of @OA\Path and update references.

### Consumes, produces field is removed from OpenAPI specification

Use @OA\MediaType to set data format.

### Rename parameter references

Rename `#/parameters/{parameter_name}` to `#/components/parameters/{parameter_name}`

### Rename response references

Rename `#/responses/{response}` to `#/components/responses/{response}`

### Renamed commandline utility

Renamed swagger to openapi

### More details about differences:

[A Visual Guide to What's New in Swagger 3.0](https://blog.readme.io/an-example-filled-guide-to-swagger-3-2/)
