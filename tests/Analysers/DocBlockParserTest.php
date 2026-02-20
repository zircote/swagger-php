<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Analysers;

use OpenApi\Annotations\Contact;
use OpenApi\Annotations\Parameter;
use OpenApi\Tests\ConstantsTest;
use OpenApi\Tests\OpenApiTestCase;

final class DocBlockParserTest extends OpenApiTestCase
{
    public const SWG_ALIAS = ['swg' => 'OpenApi\Annotations'];

    public function testParseContents(): void
    {
        $annotations = $this->annotationsFromDocBlockParser('@OA\Parameter(description="This is my parameter")', self::SWG_ALIAS);
        $this->assertNotEmpty($annotations);
        $parameter = $annotations[0];
        $this->assertInstanceOf(Parameter::class, $parameter);
        $this->assertSame('This is my parameter', $parameter->description);
    }

    public function testExtraAliases(): void
    {
        $extraAliases = [
            'contact' => Contact::class, // use OpenApi\Annotations\Contact;
            'ctest' => ConstantsTest::class, // use OpenApi\Tests\ConstantsTest as CTest;
        ];
        $annotations = $this->annotationsFromDocBlockParser('@Contact(url=CTest::URL)', $extraAliases);
        $this->assertSame('http://example.com', $annotations[0]->url);
    }
}
