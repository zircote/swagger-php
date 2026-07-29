<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

class RefInstanceController
{
    #[OA\Operation\Get(path: '/ref-instance-schema')]
    #[OA\Response(response: 200, description: 'OK', content: [
        new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: new OA\Schema\Ref(ref: RefTarget::class))),
    ])]
    public function schemaRef()
    {
    }

    #[OA\Operation\Get(path: '/ref-instance-response')]
    #[OA\Response(ref: new OA\Schema\Ref(ref: '#/components/responses/SharedResponse'), response: 200)]
    public function responseRef()
    {
    }
}
