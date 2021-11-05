<?php declare(strict_types=1);

namespace OpenApi\Tests\Fixtures;

use OpenApi\Tests\Annotations as OAF;

/**
 * @OA\Info(title="Custom annotation attributes", version="1.0")
 *
 * @OA\PathItem(path="/")
 *
 * @OA\Schema(
 *   schema="UsingCustomAttribute",
 *   required={"name"},
 *   @OAF\CustomAttribute(value=1),
 *   @OAF\CustomAttribute(value={"foo"=false, "ping"="pong"})
 * )
 */
class UsingCustomAttributes
{
}
