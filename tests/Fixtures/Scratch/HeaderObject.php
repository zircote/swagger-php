<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Info(title: 'HeaderObject', version: '1.0')]
#[OAT\Header(
    header: 'X-Request-Id',
    required: true,
    schema: new OAT\Schema(type: 'string', format: 'uuid'),
)]
class HeaderObjectController
{
    #[OAT\Get(
        path: '/endpoint',
        operationId: 'endpoint',
        responses: [
            new OAT\Response(
                response: 200,
                description: 'OK',
                headers: [
                    new OAT\Header(
                        header: 'X-Rate-Limit-Limit',
                        description: 'The number of allowed requests in the current period',
                        schema: new OAT\Schema(type: 'integer'),
                        example: 100,
                    ),
                    new OAT\Header(
                        header: 'X-Expires-After',
                        style: 'simple',
                        explode: true,
                        schema: new OAT\Schema(type: 'string', format: 'date-time'),
                        examples: [
                            new OAT\Examples(example: 'midnight', summary: 'End of day', value: '2027-01-01T00:00:00Z'),
                        ],
                    ),
                    new OAT\Header(
                        header: 'X-Request-Id',
                        ref: '#/components/headers/X-Request-Id',
                    ),
                    new OAT\Header(
                        header: 'X-Complex',
                        content: [
                            new OAT\MediaType(
                                mediaType: 'application/json',
                                schema: new OAT\Schema(type: 'object'),
                            ),
                        ],
                    ),
                ],
            ),
        ],
    )]
    public function endpoint(): void
    {
    }
}
