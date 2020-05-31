<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApiTests;

class UtilTest extends OpenApiTestCase
{
    public function testExclude()
    {
        $openapi = \OpenApi\scan(__DIR__.'/Fixtures', [
            'exclude' => [
                'Customer.php',
                'CustomerInterface.php',
                'GrandAncestor.php',
                'Parser',
                'UsingRefs.php',
                'UsingPhpDoc.php',
            ]
        ]);
        $this->assertSame('Fixture for ParserTest', $openapi->info->title, 'No errors about duplicate @OA\Info() annotations');
    }
}
