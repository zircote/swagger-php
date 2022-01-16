<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Analysers;

use Composer\Autoload\ClassLoader;
use OpenApi\Analysers\ComposerAutoloaderScanner;
use OpenApi\Tests\OpenApiTestCase;

class ComposerAutoloaderScannerTest extends OpenApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $classMap = [
            'OpenApi\Tests\\Scanners\\Foo' => __FILE__,
            'OpenApi\Tests\\Scanners\\Bar' => __FILE__,
            'Other\\Duh' => __FILE__,
        ];
        $mockClassloader  = new ClassLoader();
        $mockClassloader->addClassMap($classMap);
        spl_autoload_register([$mockClassloader, 'findFile'], true, true);
    }

    public function testComposerClassloader(): void
    {
        $expected = [
            'OpenApi\Tests\\Scanners\\Foo',
            'OpenApi\Tests\\Scanners\\Bar',
        ];
        $result = (new ComposerAutoloaderScanner())->scan(['OpenApi\Tests']);
        $this->assertEquals($expected, $result);
    }
}
