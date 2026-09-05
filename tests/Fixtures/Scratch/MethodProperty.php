<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class MethodProperty
{
    /**
     * The identifier.
     */
    #[OAT\Property(property: 'id')]
    public function getId(): int
    {
        return 0;
    }

    // a method supplies no name, so `property` is required here
    #[OAT\Property(property: 'name')]
    public function getName(): ?string
    {
        return null;
    }
}

#[OAT\Info(
    title: 'Method Property Scratch',
    version: '1.0'
)]
#[OAT\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    operationId: 'getMethodProperty',
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
class MethodPropertyEndpoint
{
}
