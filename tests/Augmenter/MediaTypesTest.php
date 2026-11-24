<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Concerns\ExpectsLogEntries;
use PHPUnit\Framework\TestCase;

final class MediaTypesTest extends TestCase
{
    use ExpectsLogEntries;

    /**
     * The compiler keys the encodings, and reports one without a name, so the list stays as written.
     */
    public function testLeavesAnEncodingListAsItIs(): void
    {
        $spec = new Specification();

        $encodings = [
            new OA\Encoding(encoding: 'metadata', contentType: 'application/xml'),
            new OA\Encoding(contentType: 'text/plain'),
        ];
        $mediaType = new OA\MediaType(mediaType: 'multipart/form-data', schema: new OA\Schema(type: 'object'), encoding: $encodings);

        $spec->operations[] = new OA\Operation(
            path: '/upload',
            method: 'post',
            requestBody: new OA\RequestBody(content: [$mediaType]),
        );

        (new Augmenter\MediaTypes())($spec);

        $this->assertSame($encodings, $mediaType->encoding);
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

    /**
     * Built through the whole pipeline, an encoding without a name is reported, not dropped silently.
     */
    public function testABuildReportsAnUnnamedEncoding(): void
    {
        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->setLogger($this->trackingLogger())
            ->withSpecification(function (Specification $specification): void {
                $specification->add(
                    new OA\Info(title: 'T', version: '1.0'),
                    new OA\Operation\Post(path: '/upload', requestBody: new OA\RequestBody(content: [new OA\MediaType(
                        mediaType: 'multipart/form-data',
                        encoding: [new OA\Encoding(encoding: 'avatar', contentType: 'image/png'), new OA\Encoding(contentType: 'text/plain')],
                    )]), responses: [new OA\Response(response: 200, description: 'ok')]),
                );
            })
            ->build();

        $this->assertSame(['avatar' => ['contentType' => 'image/png']], $result->toArray()['paths']['/upload']['post']['requestBody']['content']['multipart/form-data']['encoding']);
        $this->expectLogEntry('Encoding is missing the key field encoding, omitted', 'error');
    }
}
