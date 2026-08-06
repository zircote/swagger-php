<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'PropertyInheritance')]
class PropertyInheritanceSpec extends AbstractBaseClass
{
    #[OA\Property(property: 'inheritedfilter')]
    public string $filters;
}

#[OA\Info(title: 'Property Inheritance Scratch', version: '1.0')]
#[OA\Operation\Get(
    path: '/api/endpoint',
    operationId: 'getInheritedFilters',
)]
#[OA\Response(
    response: 200,
    description: 'successful operation',
    content: new OA\MediaType\Json(ref: PropertyInheritanceSpec::class)
)]
class PropertyInheritanceEndpointSpec
{
}
