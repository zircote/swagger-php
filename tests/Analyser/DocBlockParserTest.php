<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Analyser;

use OpenApi\Analyser;
use OpenApi\Tests\OpenApiTestCase;

class DocBlockParserTest extends OpenApiTestCase
{
    public function testParseContents()
    {
        $annotations = $this->parseComment('@OA\Parameter(description="This is my parameter")');
        $this->assertIsArray($annotations);
        $parameter = $annotations[0];
        $this->assertInstanceOf('OpenApi\Annotations\Parameter', $parameter);
        $this->assertSame('This is my parameter', $parameter->description);
    }

    public function testDeprecatedAnnotationWarning()
    {
        Analyser::$defaultImports['swg'] = 'OpenApi\Annotations';
        $this->assertOpenApiLogEntryContains('The annotation @SWG\Definition() is deprecated.');
        $this->parseComment('@SWG\Definition()', $this->getLogger(true));
        unset(Analyser::$defaultImports['swg']);
    }
}
