<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

class UtilTest extends SwaggerTestCase
{
    public function testExclude()
    {
        $openapi = \Swagger\scan(__DIR__.'/Fixtures', ['exclude' => ['Customer.php', 'UsingRefs.php', 'UsingPhpDoc.php', 'DynamicReference.php', 'GrandAncestor.php']]);
        $this->assertSame('Fixture for ParserTest', $openapi->info->title, 'No errors about duplicate @OAS\Info() annotations');
    }
}
