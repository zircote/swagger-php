<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace App;

use OpenApi\Spec as OA;

/**
 * Uses the custom augmenter `SchemaQueryParameter` to convert a vendor extension into query parameters.
 *
 * The parameters are extracted from the schema referenced by the custom extension.
 */
#[OA\Info(title: 'Example of using a custom augmenter in swagger-php', version: '1.0.0')]
class OpenApi
{
}
