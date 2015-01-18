<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

class ScanTest extends SwaggerTestCase {

    function test_scan() {
        $swagger = \Swagger\scan(__DIR__ . '/../Examples/petstore-simple');
//        echo json_encode($swagger->jsonSerialize(), JSON_PRETTY_PRINT);
    }

}
