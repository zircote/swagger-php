<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;


class ResponseTest extends SwaggerTestCase {

    function testMisspelledDefault() {
        $annotations = $this->parseComment('@SWG\Response(status="Default", description="description")');
        $this->assertSwaggerLogEntryStartsWith('Invalid value "Default" for @SWG\Response()->status, expecting "default" or a HTTP Status Code in ');
        $annotations[0]->validate();

    }

}