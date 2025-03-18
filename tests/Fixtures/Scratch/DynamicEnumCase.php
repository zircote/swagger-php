<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Info(title: 'DynamicEnumCase', version: '1.0')]
#[OAT\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
class DynamicEnumCaseEndpoint
{
}

interface DynamicEnumCaseInterface
{
    public const SOME_CONST = 'foo';
}

#[OAT\Schema(type: 'string')]
enum DynamicEnumCase: string
{
    case Foo = 'case_' . DynamicEnumCaseInterface::SOME_CONST;
    case Bar = 'case_bar';
}
