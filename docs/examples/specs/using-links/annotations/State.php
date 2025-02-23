<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingLinks\Annotations;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     type="string",
 *     enum={"OPEN", "MERGED", "DECLINED"}
 * )
 */
class State
{
}
