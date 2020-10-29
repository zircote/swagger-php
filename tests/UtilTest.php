<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use InvalidArgumentException;
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

    public function testInclude()
    {
        $openapi = \OpenApi\scan(__DIR__.'/Fixtures', [
            'exclude' => [
                'Customer.php',
                'CustomerInterface.php',
                'ThirdPartyAnnotations.php',
                'GrandAncestor.php',
                'InheritProperties',
                'Parser',
                'Processors',
                'UsingRefs.php',
                'UsingPhpDoc.php',
            ],
            'include' => [
                __DIR__.'/AdditionalFixtures',
            ],
        ]);

        $this->assertSame('Fixture for Inclusion Test', $openapi->info->title, 'No errors about duplicate @OA\Info() annotations');
    }

    public function testIncludeAndExclude()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot include and exclude the same paths.');

        \OpenApi\scan(__DIR__.'/Fixtures', [
            'exclude' => [
                'Customer.php',
                'CustomerInterface.php',
                'ThirdPartyAnnotations.php',
                'GrandAncestor.php',
                'InheritProperties',
                'Parser',
                'Processors',
                'UsingRefs.php',
                'UsingPhpDoc.php',
                __DIR__.'/AdditionalFixtures',
            ],
            'include' => [
                __DIR__.'/AdditionalFixtures',
            ],
        ]);
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
