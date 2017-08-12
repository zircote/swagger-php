<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Util;

class UtilTest extends SwaggerTestCase
{
    public function testExclude()
    {
        $openapi = \Swagger\scan(__DIR__ . '/Fixtures', ['exclude' => ['Customer.php', 'UsingRefs.php', 'UsingPhpDoc.php', 'GrandAncestor.php']]);
        $this->assertSame('Fixture for ParserTest', $openapi->info->title, 'No errors about duplicate @SWG\Info() annotations');
    }
}
