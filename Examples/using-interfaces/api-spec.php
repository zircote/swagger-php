<?php

namespace OpenApi\Examples\UsingInterfaces;

/**
 * Merging of interfaces got a bit of an overhaul in 3.0.4/5.
 *
 * By default interface annotations are now inherited via `allOf`. This is done by the `InheritInterfaces` processor.
 */

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Example of using interfaces in swagger-php",
 *     description="Using interfaces",
 *     @OA\Contact(name="Swagger API Team")
 * )
 * @OA\Server(
 *     url="https://example.localhost",
 *     description="API server"
 * )
 * @OA\Tag(
 *     name="api",
 *     description="API operations"
 * )
 */
class OpenApiSpec
{
}
