<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Info(title: 'DuplicateRef', version: '1.0')]
#[OA\Operation\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    operationId: 'getEndpoint',
)]
#[OA\Response(response: 200, description: 'OK')]
class DuplicateRefEndpointSpec
{
}

#[OA\Schema(
    schema: 'abstract-user',
    properties: [
        new OA\Property(property: 'name', schema: new OA\Schema(type: 'string')),
        new OA\Property(property: 'email', schema: new OA\Schema(type: 'string')),
    ]
)]
class AbstractUserSpec
{
}

#[OA\Schema(
    schema: 'create-user',
    allOf: [
        new OA\Schema(ref: '#/components/schemas/abstract-user'),
        new OA\Schema(required: ['name', 'email']),
    ]
)]
class CreateUserSpec extends AbstractUserSpec
{
}

// the same duplicate written as a FQCN: the inheritance augmenter adds the component
// pointer, the attribute names the class, and both resolve to `abstract-user`
#[OA\Schema(
    schema: 'update-user',
    allOf: [
        new OA\Schema(ref: AbstractUserSpec::class),
        new OA\Schema(required: ['name']),
    ]
)]
class UpdateUserSpec extends AbstractUserSpec
{
}
