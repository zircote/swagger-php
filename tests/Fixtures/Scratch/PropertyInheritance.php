<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class PropertyInheritance extends AbstractBaseClass
{
    #[OAT\Property(property: 'inheritedfilter')]
    public string $filters;
}

#[OAT\Info(title: 'Property Inheritance Scratch', version: '1.0')]
#[OAT\Get(path: '/api/endpoint')]
#[OAT\Response(
    response: 200,
    description: 'successful operation',
    content: new OAT\JsonContent(ref: PropertyInheritance::class)
)]
class PropertyInheritanceEndpoint
{
}
