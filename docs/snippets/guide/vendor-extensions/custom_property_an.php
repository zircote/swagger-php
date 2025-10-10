<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Example",
 *     version="1.0.0",
 *     x={
 *         "some-name": "a-value",
 *         "another": 2,
 *         "complex-type": {
 *             "supported": {
 *                 {"version": "1.0", "level": "baseapi"},
 *                 {"version": "2.1", "level": "fullapi"},
 *             }
 *         }
 *     }
 * )
 */
class OpenApiSpec
{
}
