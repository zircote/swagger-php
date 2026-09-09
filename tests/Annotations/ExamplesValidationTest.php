<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Tests\OpenApiTestCase;

final class ExamplesValidationTest extends OpenApiTestCase
{
    public function testSchemaExampleWithoutValue(): void
    {
        $this->expectLogEntry('Missing required field "value" for @OA\Examples() in ');

        $annotations = $this->annotationsFromDocBlockParser('@OA\Schema(schema="s", @OA\Examples(summary="no value here"))', [], '3.1.1');
        $annotations[0]->examples[0]->validate(version: '3.1.1');
    }

    public function testSchemaExampleWithValue(): void
    {
        $annotations = $this->annotationsFromDocBlockParser('@OA\Schema(schema="s", @OA\Examples(value="yo"))', [], '3.1.1');
        $annotations[0]->examples[0]->validate(version: '3.1.1');
    }

    public function testMediaTypeExampleWithoutValue(): void
    {
        $annotations = $this->annotationsFromDocBlockParser('@OA\MediaType(mediaType="application/json", @OA\Examples(example="e", externalValue="http://localhost/e.json"))');
        $annotations[0]->examples[0]->validate(version: '3.1.1');
    }

    public function testValueAndExternalValue(): void
    {
        $this->expectLogEntry('@OA\Examples(example="e") value and externalValue are mutually exclusive in ');

        $annotations = $this->annotationsFromDocBlockParser('@OA\Examples(example="e", value="yo", externalValue="http://localhost/e.json")');
        $annotations[0]->validate(version: '3.1.1');
    }
}
