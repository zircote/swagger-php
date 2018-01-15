<?php

/**
 * @OAS\OpenApi(
 *     @OAS\Info(
 *         version="1.0.0",
 *         title="Swagger Petstore",
 *         @OAS\License(name="MIT")
 *     ),
 *     host="petstore.swagger.io",
 *     basePath="/v1",
 *     schemes={"http"},
 *     consumes={"application/json"},
 *     produces={"application/json"},
 *     @OAS\Schema(
 *         definition="Error",
 *         required={"code", "message"},
 *         @OAS\Property(
 *             property="code",
 *             type="integer",
 *             format="int32"
 *         ),
 *         @OAS\Property(
 *             property="message",
 *             type="string"
 *         )
 *     ),
 *     @OAS\Schema(definition="Pets",
 *         type="array",
 *         @OAS\Items(ref="#/definitions/Pet")
 *     )
 * )
 */
