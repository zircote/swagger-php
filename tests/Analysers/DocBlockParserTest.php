<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Analysers;

use OpenApi\Tests\OpenApiTestCase;

class DocBlockParserTest extends OpenApiTestCase
{
    public const SWG_ALIAS = ['swg' => 'OpenApi\Annotations'];

    public function testParseContents(): void
    {
        $annotations = $this->annotationsFromDocBlockParser('@OA\Parameter(description="This is my parameter")', self::SWG_ALIAS);
        $this->assertNotEmpty($annotations);
        $parameter = $annotations[0];
        $this->assertInstanceOf('OpenApi\Annotations\Parameter', $parameter);
        $this->assertSame('This is my parameter', $parameter->description);
    }

    public function testExtraAliases(): void
    {
        $extraAliases = [
            'contact' => 'OpenApi\Annotations\Contact', // use OpenApi\Annotations\Contact;
            'ctest' => 'OpenApi\Tests\ConstantsTest', // use OpenApi\Tests\ConstantsTest as CTest;
        ];
        $annotations = $this->annotationsFromDocBlockParser('@Contact(url=CTest::URL)', $extraAliases);
        $this->assertSame('http://example.com', $annotations[0]->url);
    }
}
