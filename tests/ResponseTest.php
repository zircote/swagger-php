<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApiTests;

class ResponseTest extends OpenApiTestCase
{
    public function testMisspelledDefault()
    {
        $annotations = $this->parseComment('@OA\Get(@OA\Response(response="Default", description="description"))');
        $this->assertOpenApiLogEntryStartsWith('Invalid value "Default" for @OA\Response()->response, expecting "default" or a HTTP Status Code in ');
        $annotations[0]->validate();
    }
}
