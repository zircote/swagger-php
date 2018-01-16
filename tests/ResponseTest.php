<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

class ResponseTest extends SwaggerTestCase
{
    public function testMisspelledDefault()
    {
        $annotations = $this->parseComment('@OAS\Get(@OAS\Response(response="Default", description="description"))');
        $this->assertSwaggerLogEntryStartsWith('Invalid value "Default" for @OAS\Response()->response, expecting "default" or a HTTP Status Code in ');
        $annotations[0]->validate();
    }
}
