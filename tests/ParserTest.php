<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Context;
use Swagger\Parser;

class ParserTest extends SwaggerTestCase {

    function testParseContents() {
        $annotations = $this->parseComment('@SWG\Parameter(description="This is my parameter")');
        $this->assertInternalType('array', $annotations);
        $parameter = $annotations[0];
        $this->assertInstanceOf('Swagger\Annotations\Parameter', $parameter);
        $this->assertSame('This is my parameter', $parameter->description);
    }

    function testWrongCommentType() {
        $parser = new Parser();
        $this->assertSwaggerLogEntryStartsWith('Annotations are only parsed inside `/**` DocBlocks');
        $parser->parseContents("<?php\n/*\n * @SWG\Parameter() */", Context::detect());
    }

    function testThirdPartyAnnotations() {
        Parser::$whitelist = ['Swagger\\Annotations\\'];
        $parser = new Parser();
        $annotations = $parser->parseFile(__DIR__ . '/Fixtures/ThirdPartyAnnotations.php');
        $this->assertCount(2, $annotations, 'Only read the @SWG annotations, skip the others.');
        // Allow Swagger to parse 3rd party annotations
        // might contain useful info that could be extracted with a custom processor
        Parser::$whitelist[] = 'Zend\\Form\\Annotation';
        $swagger = \Swagger\scan(__DIR__ . '/Fixtures/ThirdPartyAnnotations.php');
        $this->assertSame('api/3rd-party', $swagger->paths[0]->path);
        $this->assertCount(10, $swagger->_unmerged);
    }

    function testIndentationCorrection() {
        $parser = new Parser();
        $annotations = $parser->parseFile(__DIR__ . '/Fixtures/routes.php');
        $this->assertCount(2, $annotations);
    }

    function testDeprecatedAnnotationWarning() {
        $this->assertSwaggerLogEntryStartsWith('The annotation @SWG\\Resource() is deprecated.');
        $annotations = $this->parseComment('@SWG\Resource()');
    }

}
