<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Annotations as OA;

abstract class AbstractBaseClass
{
    /** @OA\Property(property="basefilter") */
    public string $filters;
}
