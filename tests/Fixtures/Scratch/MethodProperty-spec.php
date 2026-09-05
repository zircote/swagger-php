<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'MethodProperty')]
class MethodPropertySpec
{
    /**
     * The identifier.
     */
    #[OA\Property(property: 'id')]
    public function getId(): int
    {
        return 0;
    }

    // a method supplies no name, so `property` is required here
    #[OA\Property(property: 'name')]
    public function getName(): ?string
    {
        return null;
    }
}

#[OA\Info(
    title: 'Method Property Scratch',
    version: '1.0'
)]
#[OA\Operation\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    operationId: 'getMethodProperty',
    responses: [new OA\Response(response: 200, description: 'OK')]
)]
class MethodPropertyEndpointSpec
{
}
