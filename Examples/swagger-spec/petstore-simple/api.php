<?php

/**
 * @OAS\OpenApi(
 *     @OAS\Server(
 *         url="petstore.swagger.io",
 *         description="API server"
 *     ),
 *     @OAS\Info(
 *         version="1.0.0",
 *         title="Swagger Petstore",
 *         description="A sample API that uses a petstore as an example to demonstrate features in the swagger-2.0 specification",
 *         termsOfService="http://swagger.io/terms/",
 *         @OAS\Contact(name="Swagger API Team"),
 *         @OAS\License(name="MIT")
 *     ),
 * )
 */

/**
 * @OAS\Schema(
 *     schema="ErrorModel",
 *     type="object",
 *     required={"code", "message"},
 *     @OAS\Property(
 *         property="code",
 *         type="integer",
 *         format="int32"
 *     ),
 *     @OAS\Property(
 *         property="message",
 *         type="string"
 *     )
 * )
 */