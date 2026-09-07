<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'DocblockGenericsTarget')]
class DocblockGenericsTarget
{
    #[OAT\Property]
    public string $name;
}

// The same PHP type three ways. Only the docblock differs, so all three resolve to one `$ref`.
#[OAT\Schema(schema: 'DocblockGenericsHolder')]
class DocblockGenericsHolder
{
    #[OAT\Property]
    public DocblockGenericsTarget $native;

    /** @var DocblockGenericsTarget */
    #[OAT\Property]
    public DocblockGenericsTarget $docblock;

    /** @var DocblockGenericsTarget<string> */
    #[OAT\Property]
    public DocblockGenericsTarget $generic;
}

#[OAT\Info(title: 'DocblockGenerics', version: '1.0')]
#[OAT\Get(path: '/holder', operationId: 'getHolder', responses: [
    new OAT\Response(response: 200, description: 'All good', content: new OAT\JsonContent(ref: DocblockGenericsHolder::class)),
])]
class DocblockGenericsEndpoint
{
}
