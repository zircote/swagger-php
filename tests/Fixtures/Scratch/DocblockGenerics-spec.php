<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'DocblockGenericsTarget')]
class DocblockGenericsTargetSpec
{
    #[OA\Property]
    public string $name;
}

// The same PHP type three ways. Only the docblock differs, so all three resolve to one `$ref`.
#[OA\Schema(schema: 'DocblockGenericsHolder')]
class DocblockGenericsHolderSpec
{
    #[OA\Property]
    public DocblockGenericsTargetSpec $native;

    /** @var DocblockGenericsTargetSpec */
    #[OA\Property]
    public DocblockGenericsTargetSpec $docblock;

    /** @var DocblockGenericsTargetSpec<string> */
    #[OA\Property]
    public DocblockGenericsTargetSpec $generic;
}

#[OA\Info(title: 'DocblockGenerics', version: '1.0')]
#[OA\Operation\Get(path: '/holder', operationId: 'getHolder')]
#[OA\Response(response: 200, description: 'All good', content: [
    new OA\MediaType\Json(ref: DocblockGenericsHolderSpec::class),
])]
class DocblockGenericsEndpointSpec
{
}
