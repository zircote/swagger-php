<?php declare(strict_types=1);

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
