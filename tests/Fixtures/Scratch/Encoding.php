<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;

#[OAT\Schema]
class EncodingMetadata
{
}

#[OAT\Schema]
class MultipartFormData
{
    #[OAT\Property(format: 'uuid')]
    public string $name;

    #[OAT\Property]
    public EncodingMetadata $metadata;

    #[OAT\Property(
        /*
         * This encoding is not going to show in #/components/schemas/EncodingMetadata
         * as Property::encoding is transient and expected under the media type
         */
        encoding: new OAT\Encoding(
            contentType: 'image/png, image/jpeg',
            headers: [
                new OAT\Header(
                    header: 'X-Rate-Limit-Limit',
                    description: 'The number of allowed requests in the current period',
                    schema: new OAT\Schema(
                        type: 'integer',
                    ),
                ),
            ],
        ),
    )]
    public \stdClass $avatar;
}

#[OAT\Info(title: 'Encoding', version: '1.0')]
class EncodingController
{
    #[OAT\Post(
        path: '/endpoint/multipart-form-data-ref',
        requestBody: new OAT\RequestBody(
            content: new OAT\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OAT\Schema(
                    ref: MultipartFormData::class,
                ),
                encoding: [
                    new OAT\Encoding(
                        property: 'metadata',
                        contentType: 'application/xml; charset=utf-8',
                    ),
                ]
            ),
        ),
        responses: [
            new OAT\Response(
                response: 200,
                description: 'All good',
            ),
        ]
    )]
    public function multipartFormDataRef()
    {

    }

    #[OAT\Post(
        path: '/endpoint/multipart-form-data-ref-json-content',
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(
                ref: MultipartFormData::class,
                encoding: [
                    new OAT\Encoding(
                        property: 'metadata',
                        contentType: 'application/xml; charset=utf-8',
                    ),
                ]
            ),
        ),
        responses: [
            new OAT\Response(
                response: 200,
                description: 'All good',
            ),
        ]
    )]
    public function multipartFormDataRefJsonContent()
    {

    }

    #[OAT\Post(
        path: '/endpoint/multipart-form-data-json-content',
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(
                properties: [
                    new OAT\Property(property: 'name', type: 'string', format: 'uuid'),
                    new OAT\Property(property: 'metadata', type: EncodingMetadata::class),
                    new OAT\Property(
                        property: 'avatar',
                        type: 'object',
                        encoding: new OAT\Encoding(
                            contentType: 'image/png, image/jpeg',
                        )
                    ),
                ],
                encoding: [
                    new OAT\Encoding(
                        property: 'metadata',
                        contentType: 'application/xml; charset=utf-8',
                    ),
                ],
            ),
        ),
        responses: [
            new OAT\Response(
                response: 200,
                description: 'All good',
            ),
        ]
    )]
    public function multipartFormDataJsonContent()
    {

    }

    /**
     * @OA\Post(
     *     path="/multipart-form-data-annot",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart-form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="encname"),
     *                 @OA\Property(
     *                     property="other",
     *                     @OA\Encoding(contentType="application/xml")
     *                 )
     *             ),
     *             @OA\Encoding(property="encname", contentType="application/json")
     *         )
     *     ),
     *     @OA\Response(response="200", description="OK")
     * )
     */
    public function multipartFormDataAnnot()
    {

    }
}
