<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Util;

class UtilTest extends OpenApiTestCase
{
    public function testExclude()
    {
        $openapi = \OpenApi\scan(__DIR__.'/Fixtures', [
            'exclude' => [
                'Customer.php',
                'CustomerInterface.php',
                'GrandAncestor.php',
                'InheritProperties',
                'Parser',
                'Processors',
                'UsingRefs.php',
                'UsingPhpDoc.php',
            ],
        ]);
        $this->assertSame('Fixture for ParserTest', $openapi->info->title, 'No errors about duplicate @OA\Info() annotations');
    }

    public function testRefEncode()
    {
        $this->assertSame('#/paths/~1blogs~1{blog_id}~1new~0posts', '#/paths/'.Util::refEncode('/blogs/{blog_id}/new~posts'));
    }

    public function testRefDecode()
    {
        $this->assertSame('/blogs/{blog_id}/new~posts', Util::refDecode('~1blogs~1{blog_id}~1new~0posts'));
    }
}
