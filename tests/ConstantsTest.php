<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Analyser;
use Swagger\StaticAnalyser;

class ConstantsTest extends SwaggerTestCase
{
    const URL = 'http://example.com';

    private static $counter = 0;

    public function testConstant()
    {
        self::$counter++;
        $const = 'SWAGGER_TEST_'.self::$counter;
        $this->assertFalse(defined($const));
        $this->assertSwaggerLogEntryStartsWith("[Semantical Error] Couldn't find constant ".$const);
        $this->parseComment('@SWG\Contact(email='.$const.')');

        define($const, 'me@domain.org');
        $annotations = $this->parseComment('@SWG\Contact(email='.$const.')');
        $this->assertSame('me@domain.org', $annotations[0]->email);
    }

    public function testFQCNConstant()
    {
        $annotations = $this->parseComment('@SWG\Contact(url=SwaggerTests\ConstantsTest::URL)');
        $this->assertSame('http://example.com', $annotations[0]->url);

        $annotations = $this->parseComment('@SWG\Contact(url=\SwaggerTests\ConstantsTest::URL)');
        $this->assertSame('http://example.com', $annotations[0]->url);
    }

    public function testInvalidClass()
    {
        $this->assertSwaggerLogEntryStartsWith("[Semantical Error] Couldn't find constant ConstantsTest::URL");
        $this->parseComment('@SWG\Contact(url=ConstantsTest::URL)');
    }

    public function testAutoloadConstant()
    {
        if (class_exists('Zend\Validator\Timezone', false)) {
            $this->markTestSkipped();
            $annotations = $this->parseComment('@SWG\Contact(name=Zend\Validator\Timezone::INVALID_TIMEZONE_LOCATION)');
            $this->assertSame('invalidTimezoneLocation', $annotations[0]->name);
        }
    }

    public function testDynamicImports()
    {
        $backup = Analyser::$whitelist;
        Analyser::$whitelist = false;
        $analyser = new StaticAnalyser();
        $analysis = $analyser->fromFile(__DIR__ . '/Fixtures/Customer.php');
        // @todo Only tests that $whitelist=false doesn't trigger errors,
        // No constants are used, because by default only class constants in the whitelisted namespace are allowed and no class in Swagger\Annotation namespace has a constant.

        // Scanning without whitelisting causes issues, to check uncomment next.
        // $analyser->fromFile(__DIR__ . '/Fixtures/ThirdPartyAnnotations.php');
        Analyser::$whitelist = $backup;
    }

    public function testDefaultImports()
    {
        $backup = Analyser::$defaultImports;
        Analyser::$defaultImports = [
            'contact' => 'Swagger\Annotations\Contact', // use Swagger\Annotations\Contact;
            'ctest' => 'sWaGGerTests\ConstantsTesT' // use sWaGGerTests\ConstantsTesT as CTest;
        ];
        $annotations = $this->parseComment('@Contact(url=CTest::URL)');
        $this->assertSame('http://example.com', $annotations[0]->url);
        Analyser::$defaultImports = $backup;
    }
}
