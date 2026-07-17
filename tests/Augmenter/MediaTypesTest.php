<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use PHPUnit\Framework\TestCase;

final class MediaTypesTest extends TestCase
{
    public function testRekeysEncodingsByPropertyName(): void
    {
        $spec = new Specification();

        $mediaType = new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(type: 'object'),
            encoding: [
                new OA\Encoding(encoding: 'metadata', contentType: 'application/xml'),
                new OA\Encoding(encoding: 'avatar', contentType: 'image/png'),
            ],
        );

        $spec->operations[] = new OA\Operation(
            path: '/upload',
            method: 'post',
            requestBody: new OA\RequestBody(content: [$mediaType]),
        );

        (new Augmenter\MediaTypes())($spec);

        $this->assertArrayHasKey('metadata', $mediaType->encoding);
        $this->assertArrayHasKey('avatar', $mediaType->encoding);
        $this->assertSame('application/xml', $mediaType->encoding['metadata']->contentType);
        $this->assertSame('image/png', $mediaType->encoding['avatar']->contentType);
    }

    public function testNoEncodingLeftUntouched(): void
    {
        $spec = new Specification();

        $mediaType = new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(type: 'object'),
        );

        $spec->operations[] = new OA\Operation(
            path: '/data',
            method: 'post',
            requestBody: new OA\RequestBody(content: [$mediaType]),
        );

        (new Augmenter\MediaTypes())($spec);

        $this->assertNull($mediaType->encoding);
    }

    public function testEncodingWithoutPropertyNameDiscarded(): void
    {
        $spec = new Specification();

        $mediaType = new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(type: 'object'),
            encoding: [
                new OA\Encoding(contentType: 'text/plain'),
            ],
        );

        $spec->operations[] = new OA\Operation(
            path: '/upload',
            method: 'post',
            requestBody: new OA\RequestBody(content: [$mediaType]),
        );

        (new Augmenter\MediaTypes())($spec);

        $this->assertNull($mediaType->encoding);
    }

    public function testRekeysEncodingOnResponses(): void
    {
        $spec = new Specification();

        $mediaType = new OA\MediaType(
            mediaType: 'multipart/form-data',
            encoding: [
                new OA\Encoding(encoding: 'file', contentType: 'application/octet-stream'),
            ],
        );

        $spec->operations[] = new OA\Operation(
            path: '/download',
            method: 'get',
            responses: [new OA\Response(response: '200', description: 'OK', content: [$mediaType])],
        );

        (new Augmenter\MediaTypes())($spec);

        $this->assertArrayHasKey('file', $mediaType->encoding);
    }

    public function testRekeysEncodingOnReusableRequestBodies(): void
    {
        $spec = new Specification();

        $mediaType = new OA\MediaType(
            mediaType: 'multipart/form-data',
            encoding: [
                new OA\Encoding(encoding: 'doc', contentType: 'application/pdf'),
            ],
        );

        $spec->requestBodies[] = new OA\RequestBody(content: [$mediaType]);

        (new Augmenter\MediaTypes())($spec);

        $this->assertArrayHasKey('doc', $mediaType->encoding);
    }
}
