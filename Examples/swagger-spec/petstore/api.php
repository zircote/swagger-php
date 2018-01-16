<?php

/**
 * @OAS\OpenApi(
 *     @OAS\Info(
 *         version="1.0.0",
 *         title="Swagger Petstore",
 *         @OAS\License(name="MIT")
 *     ),
 *     @OAS\Server(
 *         description="Api server",
 *         url="petstore.swagger.io",
 *     ),
 * )
 */

/**
 *  @OAS\Schema(
 *      schema="Error",
 *      required={"code", "message"},
 *      @OAS\Property(
 *          property="code",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OAS\Property(
 *          property="message",
 *          type="string"
 *      )
 *  ),
 *  @OAS\Schema(
 *      schema="Pets",
 *      type="array",
 *      @OAS\Items(ref="#/components/schemas/Pet")
 *  )
 */