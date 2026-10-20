<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\MemberErrors;

use OpenApi\Spec as OA;

#[OA\Schema]
class Pet
{
    #[OA\Property]
    public string $name;

    /**
     * A schema in `properties:` without a key names no property.
     */
    #[OA\Property(schema: new OA\Schema(type: 'object', properties: [OA\Type::String]))]
    public array $tags;
}
