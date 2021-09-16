<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Analysers\DocBlockParser;
use OpenApi\Analysers\TokenAnalyser;

class ConstantsTest extends OpenApiTestCase
{
    const URL = 'http://example.com';

    private static $counter = 0;

    public function testConstant()
    {
        self::$counter++;
        $const = 'OPENAPI_TEST_' . self::$counter;
        $this->assertFalse(defined($const));
        $this->assertOpenApiLogEntryContains("[Semantical Error] Couldn't find constant " . $const);
        $this->annotationsFromDocBlock('@OA\Contact(email=' . $const . ')');

        define($const, 'me@domain.org');
        $annotations = $this->annotationsFromDocBlock('@OA\Contact(email=' . $const . ')');
        $this->assertSame('me@domain.org', $annotations[0]->email);
    }

    public function testFQCNConstant()
    {
        $annotations = $this->annotationsFromDocBlock('@OA\Contact(url=OpenApi\Tests\ConstantsTest::URL)');
        $this->assertSame('http://example.com', $annotations[0]->url);

        $annotations = $this->annotationsFromDocBlock('@OA\Contact(url=\OpenApi\Tests\ConstantsTest::URL)');
        $this->assertSame('http://example.com', $annotations[0]->url);
    }

    public function testInvalidClass()
    {
        $this->assertOpenApiLogEntryContains("[Semantical Error] Couldn't find constant ConstantsTest::URL");
        $this->annotationsFromDocBlock('@OA\Contact(url=ConstantsTest::URL)');
    }

    public function testAutoloadConstant()
    {
        if (class_exists('AnotherNamespace\Annotations\Constants', false)) {
            $this->markTestSkipped();
        }
        $annotations = $this->annotationsFromDocBlock('@OA\Contact(name=AnotherNamespace\Annotations\Constants::INVALID_TIMEZONE_LOCATION)');
        $this->assertSame('invalidTimezoneLocation', $annotations[0]->name);
    }

    public function testDynamicImports()
    {
        $backup = DocBlockParser::$whitelist;
        DocBlockParser::$whitelist = false;
        $analyser = new TokenAnalyser();
        $analysis = $analyser->fromFile(__DIR__ . '/Fixtures/Customer.php', $this->getContext());
        // @todo Only tests that $whitelist=false doesn't trigger errors,
        // No constants are used, because by default only class constants in the whitelisted namespace are allowed and no class in OpenApi\Annotation namespace has a constant.

        // Scanning without whitelisting causes issues, to check uncomment next.
        // $analyser->fromFile(__DIR__ . '/Fixtures/ThirdPartyAnnotations.php', $this->getContext());
        DocBlockParser::$whitelist = $backup;
    }

    public function testDefaultImports()
    {
        $backup = DocBlockParser::$defaultImports;
        DocBlockParser::$defaultImports = [
            'contact' => 'OpenApi\Annotations\Contact', // use OpenApi\Annotations\Contact;
            'ctest' => 'OpenApi\Tests\ConstantsTesT', // use OpenApi\Tests\ConstantsTesT as CTest;
        ];
        $annotations = $this->annotationsFromDocBlock('@Contact(url=CTest::URL)');
        $this->assertSame('http://example.com', $annotations[0]->url);
        DocBlockParser::$defaultImports = $backup;
    }
}
