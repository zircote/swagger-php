<?php

/**
 * @SWG\Swagger(
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Swagger Petstore",
 *         @SWG\License(name="MIT")
 *     ),
 *     host="petstore.swagger.io",
 *     basePath="/v1",
 *     schemes={"http"},
 *     consumes={"application/json"},
 *     produces={"application/json"},
 *     @SWG\Definition(
 *         definition="Error",
 *         required={"code", "message"},
 *         @SWG\Property(
 *             property="code",
 *             type="integer",
 *             format="int32"
 *         ),
 *         @SWG\Property(
 *             property="message",
 *             type="string"
 *         )
 *     ),
 *     @SWG\Definition(definition="Pets",
 *         type="array",
 *         @SWG\Items(ref="#/definitions/Pet")
 *     )
 * )
 */
