<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Exception;
use Swagger\Annotations\Swagger;

class AbstractAnnotationTest extends SwaggerTestCase {

    function test_vendor_fields() {
        $annotations = $this->parseComment('@SWG\Get(x={"internal-id": 123})');
        $output = $annotations[0]->jsonSerialize();
        $this->assertSame(123, $output['x-internal-id']);
    }

    function test_invalid_field() {
        $this->setExpectedException('PHPUnit_Framework_Error_Warning', 'Skipping field "doesnot" for @SWG\\Get(), expecting ');
        $this->parseComment('@SWG\Get(doesnot="exist")');
    }

    function test_umerged_annotation() {
        $swagger = new Swagger([]);
        $swagger->merge($this->parseComment('@SWG\Parameter()'));
        $this->setExpectedException('PHPUnit_Framework_Error_Notice', 'Unexpected @SWG\Parameter(), expected to be inside @SWG\\');
        $swagger->validate();
    }

}
