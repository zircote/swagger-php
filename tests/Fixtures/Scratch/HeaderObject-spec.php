<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

// A spec header becomes a component by merging into a sibling Components; the key is
// explicit here, inferred from the class name otherwise (ComponentNames covers that).
#[OA\Components]
#[OA\Header(
    header: 'X-Request-Id',
    required: true,
    schema: new OA\Schema(type: 'string', format: 'uuid'),
)]
class HeaderObjectRequestIdSpec
{
}

#[OA\Info(title: 'HeaderObject', version: '1.0')]
class HeaderObjectControllerSpec
{
    // Stacked siblings: the Headers merge into the single Response, which merges
    // into the Get.
    #[OA\Operation\Get(path: '/endpoint', operationId: 'endpoint')]
    #[OA\Response(response: 200, description: 'OK')]
    #[OA\Header(
        header: 'X-Rate-Limit-Limit',
        description: 'The number of allowed requests in the current period',
        schema: new OA\Schema(type: 'integer'),
        example: 100,
    )]
    #[OA\Header(
        header: 'X-Expires-After',
        style: 'simple',
        explode: true,
        schema: new OA\Schema(type: 'string', format: 'date-time'),
        examples: [
            new OA\Example(example: 'midnight', summary: 'End of day', value: '2027-01-01T00:00:00Z'),
        ],
    )]
    #[OA\Header(
        header: 'X-Request-Id',
        ref: '#/components/headers/X-Request-Id',
    )]
    #[OA\Header(
        header: 'X-Complex',
        content: new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(type: 'object'),
        ),
    )]
    public function endpoint(): void
    {
    }
}
