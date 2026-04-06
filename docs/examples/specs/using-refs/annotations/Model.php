<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingRefs\Annotations;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(description="A model")
 *
 * This is here to force the use of allOf in the Product schema.
 */
class Model
{
}
