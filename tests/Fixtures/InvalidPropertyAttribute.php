<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OA;

class InvalidPropertyAttribute
{
    #[OA\Property(required: 'yes')] // required has to be a bool, array or null
    public function post()
    {
    }
}
