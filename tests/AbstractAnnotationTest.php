<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Annotations\Swagger;

class AbstractAnnotationTest extends SwaggerTestCase {

    function testVendorFields() {
        $annotations = $this->parseComment('@SWG\Get(x={"internal-id": 123})');
        $output = $annotations[0]->jsonSerialize();
        $prefixedProperty = 'x-internal-id';
        $this->assertSame(123, $output->$prefixedProperty);
    }

    function testInvalidField() {
        $this->assertSwaggerLogEntryStartsWith('Unexpected field "doesnot" for @SWG\Get(), expecting');
        $this->parseComment('@SWG\Get(doesnot="exist")');
    }

    function testUmergedAnnotation() {
        $swagger = $this->createSwaggerWithInfo();
        $swagger->merge($this->parseComment('@SWG\Parameter()'));
        $this->assertSwaggerLogEntryStartsWith('Unexpected @SWG\Parameter(), expected to be inside @SWG\\');
        $swagger->validate();
    }

    function testConflictedNesting() {
        $comment = <<<END
@SWG\Info(
    title="Info only has one contact field..",
    version="test",
    @SWG\Contact(name="first"),
    @SWG\Contact(name="second")
)
END;
        $annotations = $this->parseComment($comment);
        $this->assertSwaggerLogEntryStartsWith('Multiple @SWG\Contact() not allowed for @SWG\Info() in:');
        $annotations[0]->validate();
    }

    function testKey() {
        $comment = <<<END
@SWG\Response(
    @SWG\Header(header="X-CSRF-Token",description="Token to prevent Cross Site Request Forgery")
)
END;
        $annotations = $this->parseComment($comment);
        $this->assertEquals('{"headers":{"X-CSRF-Token":{"description":"Token to prevent Cross Site Request Forgery"}}}', json_encode($annotations[0]));
    }

    function testConflictingKey() {
        $comment = <<<END
@SWG\Response(
    description="The headers in response must have unique header values",
    @SWG\Header(header="X-CSRF-Token", type="string", description="first"),
    @SWG\Header(header="X-CSRF-Token", type="string", description="second")
)
END;
        $annotations = $this->parseComment($comment);
        $this->assertSwaggerLogEntryStartsWith('Multiple @SWG\Header() with the same header value in:');
        $annotations[0]->validate();
    }
    
    function testRequiredFields() {
        $annotations = $this->parseComment('@SWG\Info()');
        $info = $annotations[0];
        $this->assertSwaggerLogEntryStartsWith('Missing required field "title" for @SWG\Info() in ');
        $this->assertSwaggerLogEntryStartsWith('Missing required field "version" for @SWG\Info() in ');
        $info->validate();
    }
}
