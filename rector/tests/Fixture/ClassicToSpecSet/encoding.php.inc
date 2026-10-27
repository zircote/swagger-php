<?php

namespace OpenApi\Rector\Tests\Fixture\ClassicToSpecSet;

use OpenApi\Attributes as OA;

#[OA\Schema]
class Upload
{
    #[OA\Property(type: 'string', format: 'binary', encoding: new OA\Encoding(property: 'file', contentType: 'image/png'))]
    public $file;
}

#[OA\Post(path: '/u', requestBody: new OA\RequestBody(content: new OA\MediaType(mediaType: 'multipart/form-data',
    schema: new OA\Schema(ref: Upload::class), encoding: [new OA\Encoding('file', 'image/png')])),
    responses: [new OA\Response(response: 200, description: 'ok')])]
class UploadController
{
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\ClassicToSpecSet;

use OpenApi\Spec as OAS;

#[OAS\Schema]
class Upload
{
    #[OAS\Property\Encoded(encoding: new OAS\Encoding(encoding: 'file', contentType: 'image/png'), schema: new OAS\Schema(type: 'string', format: 'binary'))]
    public $file;
}

#[OAS\Operation\Post(path: '/u', requestBody: new OAS\RequestBody(content: new OAS\MediaType(mediaType: 'multipart/form-data',
    schema: new OAS\Schema(ref: Upload::class), encoding: [new OAS\Encoding(encoding: 'file', contentType: 'image/png')])),
    responses: [new OAS\Response(response: 200, description: 'ok')])]
class UploadController
{
}

?>
