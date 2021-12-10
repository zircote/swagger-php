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
                        'uses' => ['OA' => 'OpenApi\\Annotations'],
                        'interfaces' => [],
                        'traits' => [],
                        'methods' => [],
                        'properties' => [],
                    ],
                    'OpenApi\\Tests\\Fixtures\\Apis\\DocBlocks\\Product' => [
                        'uses' => ['OA' => 'OpenApi\\Annotations'],
                        'interfaces' => ['OpenApi\\Tests\\Fixtures\\Apis\\DocBlocks\\ProductInterface'],
                        'traits' => ['OpenApi\\Tests\\Fixtures\\Apis\\DocBlocks\\NameTrait'],
                        'methods' => [],
                        'properties' => ['id'],
                    ],
                    'OpenApi\\Tests\\Fixtures\\Apis\\DocBlocks\\ProductController' => [
                        'uses' => ['OA' => 'OpenApi\\Annotations'],
                        'interfaces' => [],
                        'traits' => [],
                        'methods' => ['getProduct', 'addProduct'],
                        'properties' => [],
                    ],
                    'OpenApi\\Tests\\Fixtures\\Apis\\DocBlocks\\ProductInterface' => [
                        'uses' => ['OA' => 'OpenApi\\Annotations'],
                        'interfaces' => [],
                        'traits' => [],
                        'methods' => [],
                        'properties' => [],
                    ],
                    'OpenApi\\Tests\\Fixtures\\Apis\\DocBlocks\\NameTrait' => [
                        'uses' => ['OA' => 'OpenApi\\Annotations'],
                        'interfaces' => [],
                        'traits' => [],
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
                    'OpenApi\\Tests\\Fixtures\\PHP\\MethodAttr' => [
                        'uses' => [],
                        'interfaces' => [],
                        'traits' => [],
                        'methods' => [],
                        'properties' => [],
                    ],
                    'OpenApi\\Tests\\Fixtures\\PHP\\GenericAttr' => [
                        'uses' => [],
                        'interfaces' => [],
                        'traits' => [],
                        'methods' => ['__construct'],
                        'properties' => [],
                    ],
                    'OpenApi\\Tests\\Fixtures\\PHP\\Decorated' => [
                        'uses' => [],
                        'interfaces' => [],
                        'traits' => [],
                        'methods' => ['foo', 'bar'],
                        'properties' => [],
                    ],
                ],
            ],
            'ExtendsClass' => [
                'PHP/Inheritance/ExtendsClass.php',
                [
                    'OpenApi\\Tests\\Fixtures\\PHP\\Inheritance\\ExtendsClass' => [
                        'uses' => [],
                        'interfaces' => [],
                        'traits' => [],
                        'methods' => ['extendsClassFunc'],
                        'properties' => ['extendsClassProp'],
                    ],
                ],
            ],
            'ExtendsInterface' => [
                'PHP/Inheritance/ExtenedsBaseInterface.php',
                [
                    'OpenApi\\Tests\\Fixtures\\PHP\\Inheritance\\ExtenedsBaseInterface' => [
                        'uses' => [],
                        'interfaces' => ['OpenApi\\Tests\\Fixtures\\PHP\\Inheritance\\BaseInterface'],
                        'traits' => [],
                        'methods' => [],
                        'properties' => [],
                    ],
                ],
            ],
            'CustomerInterface' => [
                'CustomerInterface.php',
                [
                    'OpenApi\\Tests\\Fixtures\\CustomerInterface' => [
                        'uses' => ['OA' => 'OpenApi\\Annotations'],
                        'interfaces' => [],
                        'traits' => [],
                        'methods' => ['firstname', 'secondname', 'thirdname', 'fourthname', 'lastname', 'tags', 'submittedBy', 'friends', 'bestFriend'],
                        'properties' => [],
                    ],
                ],
            ],
            'AllTraits' => [
                'Parser/AllTraits.php',
                [
                    'OpenApi\\Tests\\Fixtures\\Parser\\AllTraits' => [
                        'uses' => [],
                        'interfaces' => [],
                        'traits' => ['OpenApi\\Tests\\Fixtures\\Parser\\AsTrait', 'OpenApi\\Tests\\Fixtures\\Parser\\HelloTrait'],
                        'methods' => [],
                        'properties' => [],
                    ],
                ],
            ],
            'User' => [
                'Parser/User.php',
                [
                    'OpenApi\\Tests\\Fixtures\\Parser\\User' => [
                        'uses' => [
                            'Hello' => 'OpenApi\\Tests\\Fixtures\\Parser\\HelloTrait',
                            'ParentClass' => 'OpenApi\\Tests\\Fixtures\\Parser\\Sub\\SubClass',
                        ],
                        'interfaces' => ['OpenApi\\Tests\\Fixtures\\Parser\\UserInterface'],
                        'traits' => ['OpenApi\\Tests\\Fixtures\\Parser\\HelloTrait'],
                        'methods' => ['getFirstName'],
                        'properties' => [],
                    ],
                ],
            ],
            'Php8NamedProperty' => [
                'PHP/Php8NamedProperty.php',
                [
                    'OpenApi\\Tests\\Fixtures\\PHP\\Php8NamedProperty' => [
                        'uses' => ['Label' => 'OpenApi\\Tests\\Fixtures\\PHP\\Label'],
                        'interfaces' => [],
                        'traits' => [],
                        'methods' => ['__construct'],
                        'properties' => [],
                    ],
                ],
            ],
            'Php8NamedArguments' => [
                'PHP/Php8NamedArguments.php',
                [
                    'OpenApi\\Tests\\Fixtures\\PHP\\Php8NamedArguments' => [
                        'uses' => [],
                        'interfaces' => [],
                        'traits' => [],
                        'methods' => ['useFoo', 'foo'],
                        'properties' => [],
                    ],
                ],
            ],
            'AnonymousFunctions' => [
                'PHP/AnonymousFunctions.php',
                [
                    'OpenApi\\Tests\\Fixtures\\PHP\\AnonymousFunctions' => [
                        'uses' => ['Info' => 'OpenApi\\Annotations\\Info'],
                        'interfaces' => [],
                        'traits' => [],
                        'methods' => [
                            'index',
                            'query',
                            'other', 'shortFn',
                            'staticShortFn',
                            'withUse',
                            'dollarCurly1', 'dollarCurly2',
                            'curlyOpen',
                        ],
                        'properties' => [],
                    ],
                ],
            ],
            'namespaces1' => [
                'PHP/namespaces1.php',
                [
                    'Foo\\FooClass' => [
                        'uses' => [],
                        'interfaces' => [],
                        'traits' => [],
                        'methods' => [],
                        'properties' => [],
                    ],
                    'Bar\\BarClass' => [
                        'uses' => [],
                        'interfaces' => [],
                        'traits' => [],
                        'methods' => [],
                        'properties' => [],
                    ],
                ],
                'namespaces2' => [
                    'PHP/namespaces2.php',
                    [
                        'Foo\\FooClass' => [
                            'uses' => [],
                            'interfaces' => [],
                            'traits' => [],
                            'methods' => [],
                            'properties' => [],
                        ],
                        'Bar\\BarClass' => [
                            'uses' => [],
                            'interfaces' => [],
                            'traits' => [],
                            'methods' => [],
                            'properties' => [],
                        ],
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
