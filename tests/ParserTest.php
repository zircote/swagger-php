<?php
namespace SwaggerTests;

class ParserTest extends \PHPUnit_Framework_TestCase{
    
    function test_parseFile() {
        $parser = new \Swagger\Parser();
        $annotations = $parser->parseFile(__DIR__.'/../Examples/petstore-simple/pets.php');
        $this->assertInternalType('array', $annotations);
        $this->assertInstanceOf('Swagger\Annotations\SwaggerAnnotation', $annotations[0]);
    }
}
