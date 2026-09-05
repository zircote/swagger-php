<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Info(title: 'DynamicEnumCase', version: '1.0')]
#[OA\Operation\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    operationId: 'dynamicEnumCase',
)]
#[OA\Response(response: 200, description: 'OK')]
class DynamicEnumCaseEndpointSpec
{
}

interface DynamicEnumCaseInterfaceSpec
{
    public const SOME_CONST = 'foo';
}

#[OA\Schema(schema: 'DynamicEnumCase', type: 'string')]
enum DynamicEnumCaseSpec: string
{
    case Foo = 'case_' . DynamicEnumCaseInterfaceSpec::SOME_CONST;
    case Bar = 'case_bar';
}
