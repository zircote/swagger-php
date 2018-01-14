<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

class AbstractAnnotationTest extends SwaggerTestCase
{
    public function testVendorFields()
    {
        $annotations = $this->parseComment('@OAS\Get(x={"internal-id": 123})');
        $output = $annotations[0]->jsonSerialize();
        $prefixedProperty = 'x-internal-id';
        $this->assertSame(123, $output->$prefixedProperty);
    }

    public function testInvalidField()
    {
        $this->assertSwaggerLogEntryStartsWith('Unexpected field "doesnot" for @OAS\Get(), expecting');
        $this->parseComment('@OAS\Get(doesnot="exist")');
    }

    public function testUmergedAnnotation()
    {
        $openapi = $this->createSwaggerWithInfo();
        $openapi->merge($this->parseComment('@OAS\Items()'));
        $this->assertSwaggerLogEntryStartsWith('Unexpected @OAS\Items(), expected to be inside @OAS\\');
        $openapi->validate();
    }

    public function testConflictedNesting()
    {
        $comment = <<<END
@OAS\Info(
    title="Info only has one contact field..",
    version="test",
    @OAS\Contact(name="first"),
    @OAS\Contact(name="second")
)
END;
        $annotations = $this->parseComment($comment);
        $this->assertSwaggerLogEntryStartsWith('Only one @OAS\Contact() allowed for @OAS\Info() multiple found in:');
        $annotations[0]->validate();
    }

    public function testKey()
    {
        $comment = <<<END
@OAS\Response(
    @OAS\Header(header="X-CSRF-Token",description="Token to prevent Cross Site Request Forgery")
)
END;
        $annotations = $this->parseComment($comment);
        $this->assertEquals('{"headers":{"X-CSRF-Token":{"description":"Token to prevent Cross Site Request Forgery"}}}', json_encode($annotations[0]));
    }

    public function testConflictingKey()
    {
        $comment = <<<END
@OAS\Response(
    description="The headers in response must have unique header values",
    @OAS\Header(header="X-CSRF-Token", @OAS\Schema(type="string"), description="first"),
    @OAS\Header(header="X-CSRF-Token", @OAS\Schema(type="string"), description="second")
)
END;
        $annotations = $this->parseComment($comment);
        $this->assertSwaggerLogEntryStartsWith('Multiple @OAS\Header() with the same header="X-CSRF-Token":');
        $annotations[0]->validate();
    }

    public function testRequiredFields()
    {
        $annotations = $this->parseComment('@OAS\Info()');
        $info = $annotations[0];
        $this->assertSwaggerLogEntryStartsWith('Missing required field "title" for @OAS\Info() in ');
        $this->assertSwaggerLogEntryStartsWith('Missing required field "version" for @OAS\Info() in ');
        $info->validate();
    }

    public function testTypeValidation()
    {
        $comment = <<<END
@OAS\Parameter(
    name=123,
    in="dunno",
    required="maybe",
    @OAS\Schema(
      type="strig",
    )
)
END;
        $annotations = $this->parseComment($comment);
        $parameter = $annotations[0];
        $this->assertSwaggerLogEntryStartsWith('@OAS\Parameter(name=123,in="dunno")->name is a "integer", expecting a "string" in ');
        $this->assertSwaggerLogEntryStartsWith('@OAS\Parameter(name=123,in="dunno")->in "dunno" is invalid, expecting "query", "header", "path", "cookie" in ');
        $this->assertSwaggerLogEntryStartsWith('@OAS\Parameter(name=123,in="dunno")->required is a "string", expecting a "boolean" in ');
//        $this->assertSwaggerLogEntryStartsWith('@OAS\Parameter(name=123,in="dunno")->maximum is a "string", expecting a "number" in ');
//        $this->assertSwaggerLogEntryStartsWith('@OAS\Parameter(name=123,in="dunno")->type must be "string", "number", "integer", "boolean", "array", "file" when @OAS\Parameter()->in != "body" in ');
        $parameter->validate();
    }
}
