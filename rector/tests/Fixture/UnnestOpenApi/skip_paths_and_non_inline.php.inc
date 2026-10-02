<?php

namespace OpenApi\Rector\Tests\Fixture\UnnestOpenApi;

use OpenApi\Attributes as OA;

#[OA\OpenApi(info: new OA\Info(title: 'Api', version: '1'), paths: [new OA\PathItem(path: '/x')])]
class WithPaths
{
}

#[OA\OpenApi(info: new OA\Info(title: 'Api', version: '1'), tags: Tags::ALL)]
class ConstantTags
{
}
