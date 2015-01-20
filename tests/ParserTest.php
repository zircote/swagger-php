<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Context;
use Swagger\Parser;

class ParserTest extends SwaggerTestCase {

    function test_parseContents() {
        $annotations = $this->parseComment('@SWG\Parameter(name="my_param")');
        $this->assertInternalType('array', $annotations);
        $parameter = $annotations[0];
        $this->assertInstanceOf('Swagger\Annotations\Parameter', $parameter);
        $this->assertSame('my_param', $parameter->name);
    }

    function testWrongCommentType() {
        $parser = new Parser();
        $annotations = $parser->parseContents('<?php\n/*\n * @SWG\Parameter() */', Context::detect());
    }

}
