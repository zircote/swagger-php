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
        $prefixedProperty = 'x-internal-id';
        $this->assertSame(123, $output->$prefixedProperty);
    }

    function test_invalid_field() {
        $this->setExpectedException('PHPUnit_Framework_Error_Warning', 'Unexpected field "doesnot" for @SWG\\Get(), expecting ');
        $this->parseComment('@SWG\Get(doesnot="exist")');
    }

    function test_umerged_annotation() {
        $swagger = new Swagger([]);
        $swagger->merge($this->parseComment('@SWG\Parameter()'));
        $this->setExpectedException('PHPUnit_Framework_Error_Notice', 'Unexpected @SWG\Parameter(), expected to be inside @SWG\\');
        $swagger->validate();
    }

    function test_conflicted_nesting() {
        $comment = <<<END
@SWG\Info(
    @SWG\Contact(name="first"),
    @SWG\Contact(name="second")
)
END;
        $annotations = $this->parseComment($comment);
        $this->setExpectedException('PHPUnit_Framework_Error_Notice', 'Multiple @SWG\Contact() not allowed for @SWG\Info() in:');
        $annotations[0]->validate();
    }

    function test_key() {
        $comment = <<<END
@SWG\Response(
    @SWG\Header(header="X-CSRF-Token",description="Token to prevent Cross Site Request Forgery")
)
END;
        $annotations = $this->parseComment($comment);
        $this->assertEquals('{"headers":{"X-CSRF-Token":{"description":"Token to prevent Cross Site Request Forgery"}}}', json_encode($annotations[0]));
    }

    function test_conflicting_key() {
        $comment = <<<END
@SWG\Response(
    @SWG\Header(header="X-CSRF-Token",description="first"),
    @SWG\Header(header="X-CSRF-Token",description="second")
)
END;
        $annotations = $this->parseComment($comment);
        $this->setExpectedException('PHPUnit_Framework_Error_Notice', 'Multiple @SWG\Header() with the same header value in:');
        $annotations[0]->validate();
    }
}
