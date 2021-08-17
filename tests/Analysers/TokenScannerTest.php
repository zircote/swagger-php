<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Analysers;

use OpenApi\Analysers\TokenScanner;
use OpenApi\Tests\OpenApiTestCase;

class TokenScannerTest extends OpenApiTestCase
{
    public function scanCases()
    {
        return [
            'basic' => [
                'Apis/DocBlocks/basic.php',
                [
                    'OpenApi\\Tests\\Fixtures\\Apis\\DocBlocks\\OpenApiSpec' => [
                        'methods' => [],
                        'properties' => [],
                    ],
                    'OpenApi\\Tests\\Fixtures\\Apis\\DocBlocks\\Product' => [
                        'methods' => [],
                        'properties' => ['id'],
                    ],
                    'OpenApi\\Tests\\Fixtures\\Apis\\DocBlocks\\ProductController' => [
                        'methods' => ['getProduct'],
                        'properties' => [],
                    ],
                    'OpenApi\\Tests\\Fixtures\\Apis\\DocBlocks\\ProductInterface' => [
                        'methods' => [],
                        'properties' => [],
                    ],
                    'OpenApi\\Tests\\Fixtures\\Apis\\DocBlocks\\NameTrait' => [
                        'methods' => [],
                        'properties' => ['name'],
                    ],
                ],
            ],
            'php7' => [
                'PHP/php7.php',
                [],
            ],
            'php8' => [
                'PHP/php8.php',
                [
                    'OpenApi\\Tests\Fixtures\\PHP\\MethodAttr' => [
                        'methods' => [],
                        'properties' => [],
                    ],
                    'OpenApi\Tests\\Fixtures\\PHP\\GenericAttr' => [
                        'methods' => ['__construct'],
                        'properties' => [],
                    ],
                    'OpenApi\\Tests\\Fixtures\\PHP\\Decorated' => [
                        'methods' => ['foo', 'bar'],
                        'properties' => [],
                    ],
                ],
            ],
            'ExtendsClass' => [
                'PHP/Inheritance/ExtendsClass.php',
                [
                    'OpenApi\\Tests\\Fixtures\\PHP\\Inheritance\\ExtendsClass' => [
                        'methods' => ['extendsClassFunc'],
                        'properties' => ['extendsClassProp'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider scanCases
     */
    public function testScanFile($fixture, $expected)
    {
        $result = (new TokenScanner())->scanFile($this->fixture($fixture));
        $this->assertEquals($expected, $result);
    }
}
