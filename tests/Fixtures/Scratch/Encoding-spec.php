<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'EncodingMetadata')]
class EncodingMetadataSpec
{
}

#[OA\Schema(schema: 'MultipartFormData')]
class MultipartFormDataSpec
{
    #[OA\Schema(format: 'uuid')]
    public string $name;

    #[OA\Property]
    public EncodingMetadataSpec $metadata;

    // This encoding is not going to show in #/components/schemas/MultipartFormData
    #[OA\Encoding(
        contentType: 'image/png, image/jpeg',
        headers: [
            new OA\Header(
                header: 'X-Rate-Limit-Limit',
                description: 'The number of allowed requests in the current period',
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
    )]
    public \stdClass $avatar;
}

#[OA\Info(title: 'Encoding', version: '1.0')]
class EncodingControllerSpec
{
    #[OA\Operation\Post(
        path: '/endpoint/multipart-form-data-ref',
        operationId: 'multipartFormDataRef',
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    ref: MultipartFormDataSpec::class,
                ),
                encoding: [
                    new OA\Encoding(
                        encoding: 'metadata',
                        contentType: 'application/xml; charset=utf-8',
                    ),
                ]
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'All good',
            ),
        ]
    )]
    public function multipartFormDataRef()
    {
    }

    #[OA\Operation\Post(
        path: '/endpoint/multipart-form-data-ref-json-content',
        operationId: 'multipartFormDataRefJsonContent',
        requestBody: new OA\RequestBody(
            content: new OA\MediaType\Json(
                ref: MultipartFormDataSpec::class,
                encoding: [
                    new OA\Encoding(
                        encoding: 'metadata',
                        contentType: 'application/xml; charset=utf-8',
                    ),
                ]
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'All good',
            ),
        ]
    )]
    public function multipartFormDataRefJsonContent()
    {
    }

    #[OA\Operation\Post(
        path: '/endpoint/multipart-form-data-json-content',
        operationId: 'multipartFormDataJsonContent',
        requestBody: new OA\RequestBody(
            content: new OA\MediaType\Json(
                properties: [
                    new OA\Property(
                        property: 'name',
                        schema: new OA\Schema(type: 'string', format: 'uuid'),
                    ),
                    new OA\Property(
                        property: 'metadata',
                        schema: new OA\Schema\Ref(EncodingMetadataSpec::class),
                    ),
                    new OA\Property\Encoded(
                        property: 'avatar',
                        encoding: new OA\Encoding(
                            contentType: 'image/png, image/jpeg',
                        ),
                    ),
                ],
                encoding: [
                    new OA\Encoding(
                        encoding: 'metadata',
                        contentType: 'application/xml; charset=utf-8',
                    ),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'All good',
            ),
        ]
    )]
    public function multipartFormDataJsonContent()
    {
    }
}
