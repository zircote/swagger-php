<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Analysers;

use OpenApi\Analysers\TokenScanner;
use OpenApi\Tests\OpenApiTestCase;

class TokenScannerTest extends OpenApiTestCase
{
    public static function scanCases(): iterable
    {
        if (\PHP_VERSION_ID >= 80100) {
            yield 'abstract' => [
                'PHP/AbstractKeyword.php',
                [
                    'OpenApi\Tests\Fixtures\PHP\AbstractKeyword' => [
                        'uses' => [
                            'OAT' => 'OpenApi\Attributes',
                        ],
                        'interfaces' => [],
                        'traits' => [],
                        'enums' => [],
                        'methods' => ['stuff', 'other', 'another'],
                        'properties' => [],
                    ],
                ],
            ];
        }
        if (\PHP_VERSION_ID >= 80100) {
            yield 'basic' => [
                'Apis/DocBlocks/basic.php',
                [
                    'OpenApi\\Tests\\Fixtures\\Apis\\DocBlocks\\OpenApiSpec' => [
                        'uses' => ['OA' => 'OpenApi\\Annotations'],
                        'interfaces' => [],
                        'traits' => [],
                        'enums' => [],
                        'methods' => [],
                        'properties' => [],
                    ],
                    'OpenApi\\Tests\\Fixtures\\Apis\\DocBlocks\\Product' => [
                        'uses' => ['OA' => 'OpenApi\\Annotations'],
                        'interfaces' => ['OpenApi\\Tests\\Fixtures\\Apis\\DocBlocks\\ProductInterface'],
                        'traits' => ['OpenApi\\Tests\\Fixtures\\Apis\\DocBlocks\\NameTrait'],
                        'enums' => [],
                        'methods' => ['__construct'],
                        'properties' => ['releasedAt', 'quantity', 'brand', 'colour', 'id'],
                    ],
                    'OpenApi\\Tests\\Fixtures\\Apis\\DocBlocks\\ProductController' => [
                        'uses' => ['OA' => 'OpenApi\\Annotations'],
                        'interfaces' => [],
                        'traits' => [],
                        'enums' => [],
                        'methods' => ['getProduct', 'addProduct', 'getAll', 'subscribe'],
                        'properties' => [],
                    ],
                    'OpenApi\\Tests\\Fixtures\\Apis\\DocBlocks\\ProductInterface' => [
                        'uses' => ['OA' => 'OpenApi\\Annotations'],
                        'interfaces' => [],
                        'traits' => [],
                        'enums' => [],
                        'methods' => [],
                        'properties' => [],
                    ],
                    'OpenApi\\Tests\\Fixtures\\Apis\\DocBlocks\\NameTrait' => [
                        'uses' => ['OA' => 'OpenApi\\Annotations'],
                        'interfaces' => [],
                        'traits' => [],
                        'enums' => [],
                        'methods' => [],
                        'properties' => ['name'],
                    ],
                    'OpenApi\\Tests\\Fixtures\\Apis\\DocBlocks\\Colour' => [
                        'uses' => ['OA' => 'OpenApi\\Annotations'],
                        'interfaces' => [],
                        'traits' => [],
                        'enums' => [],
                        'methods' => [],
                        'properties' => [],
                    ],
                    'OpenApi\\Tests\\Fixtures\\Apis\\DocBlocks\\Server' => [
                        'uses' => ['OA' => 'OpenApi\\Annotations'],
                        'interfaces' => [],
                        'traits' => [],
                        'enums' => [],
                        'methods' => [],
                        'properties' => [],
                    ],
                ],
            ];
        }

        yield 'references' => [
            'PHP/References.php',
            [
                'OpenApi\Tests\Fixtures\PHP\References' => [
                    'uses' => [
                        'OA' => 'OpenApi\Annotations',
                    ],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [
                        'return_ref',
                    ],
                    'properties' => [],
                ],
            ],
        ];

        yield 'php7' => [
            'PHP/php7.php',
            [],
        ];

        yield 'php8' => [
            'PHP/php8.php',
            [
                'OpenApi\\Tests\\Fixtures\\PHP\\MethodAttr' => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [],
                    'properties' => [],
                ],
                'OpenApi\\Tests\\Fixtures\\PHP\\GenericAttr' => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['__construct'],
                    'properties' => [],
                ],
                'OpenApi\\Tests\\Fixtures\\PHP\\Decorated' => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['foo', 'bar'],
                    'properties' => [],
                ],
            ],
        ];

        yield 'ExtendsClass' => [
            'PHP/Inheritance/ExtendsClass.php',
            [
                'OpenApi\\Tests\\Fixtures\\PHP\\Inheritance\\ExtendsClass' => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['extendsClassFunc'],
                    'properties' => ['extendsClassProp'],
                ],
            ],
        ];

        yield 'ExtendsInterface' => [
            'PHP/Inheritance/ExtenedsBaseInterface.php',
            [
                'OpenApi\\Tests\\Fixtures\\PHP\\Inheritance\\ExtenedsBaseInterface' => [
                    'uses' => [],
                    'interfaces' => ['OpenApi\\Tests\\Fixtures\\PHP\\Inheritance\\BaseInterface'],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [],
                    'properties' => [],
                ],
            ],
        ];

        yield 'CustomerInterface' => [
            'CustomerInterface.php',
            [
                'OpenApi\\Tests\\Fixtures\\CustomerInterface' => [
                    'uses' => ['OA' => 'OpenApi\\Annotations'],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['firstname', 'secondname', 'thirdname', 'fourthname', 'lastname', 'tags', 'submittedBy', 'friends', 'bestFriend'],
                    'properties' => [],
                ],
            ],
        ];

        yield 'AllTraits' => [
            'Parser/AllTraits.php',
            [
                'OpenApi\\Tests\\Fixtures\\Parser\\AllTraits' => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [
                        'OpenApi\\Tests\\Fixtures\\Parser\\AsTrait',
                        'OpenApi\\Tests\\Fixtures\\Parser\\HelloTrait',
                    ],
                    'enums' => [],
                    'methods' => [],
                    'properties' => [],
                ],
            ],
        ];

        yield 'User' => [
            'Parser/User.php',
            [
                'OpenApi\\Tests\\Fixtures\\Parser\\User' => [
                    'uses' => [
                        'Hello' => 'OpenApi\\Tests\\Fixtures\\Parser\\HelloTrait',
                        'ParentClass' => 'OpenApi\\Tests\\Fixtures\\Parser\\Sub\\SubClass',
                    ],
                    'interfaces' => ['OpenApi\\Tests\\Fixtures\\Parser\\UserInterface'],
                    'traits' => ['OpenApi\\Tests\\Fixtures\\Parser\\HelloTrait'],
                    'enums' => [],
                    'methods' => ['getFirstName'],
                    'properties' => [],
                ],
            ],
        ];

        yield 'HelloTrait' => [
            'Parser/HelloTrait.php',
            [
                'OpenApi\\Tests\\Fixtures\\Parser\\HelloTrait' => [
                    'uses' => [
                        'Aliased' => 'OpenApi\\Tests\\Fixtures\\Parser\\AsTrait',
                    ],
                    'interfaces' => [],
                    'traits' => [
                        'OpenApi\\Tests\\Fixtures\\Parser\\OtherTrait',
                        'OpenApi\\Tests\\Fixtures\\Parser\\AsTrait',
                    ],
                    'enums' => [],
                    'methods' => [],
                    'properties' => ['greet'],
                ],
            ],
        ];

        yield 'Php8PromotedProperties' => [
            'PHP/Php8PromotedProperties.php',
            [
                'OpenApi\\Tests\\Fixtures\\PHP\\Php8PromotedProperties' => [
                    'uses' => [
                        'OAT' => 'OpenApi\\Attributes',
                        'OA' => 'OpenApi\Annotations',
                    ],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['__construct'],
                    'properties' => ['labels', 'id'],
                ],
            ],
        ];

        yield 'Php8NamedArguments' => [
            'PHP/Php8NamedArguments.php',
            [
                'OpenApi\\Tests\\Fixtures\\PHP\\Php8NamedArguments' => [
                    'uses' => [
                        'OA' => 'OpenApi\Annotations',
                    ],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['useFoo', 'foo'],
                    'properties' => [],
                ],
                'OpenApi\\Tests\\Fixtures\\PHP\\ReservedWordsAttr' => [
                    'uses' => [
                        'OA' => 'OpenApi\Annotations',
                    ],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['__construct'],
                    'properties' => [],
                ],
                'OpenApi\\Tests\\Fixtures\\PHP\\UserlandClass' => [
                    'uses' => [
                        'OA' => 'OpenApi\Annotations',
                    ],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [],
                    'properties' => [],
                ],
            ],
        ];

        yield 'AnonymousFunctions' => [
            'PHP/AnonymousFunctions.php',
            [
                'OpenApi\\Tests\\Fixtures\\PHP\\AnonymousFunctions' => [
                    'uses' => [
                        'OA' => 'OpenApi\Annotations',
                    ],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
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
        ];

        yield 'CurlyBrace' => [
            'PHP/MultipleFunctions.php',
            [
                'OpenApi\Tests\Fixtures\PHP\MultipleFunctions' => [
                    'uses' => [
                        'OA' => 'OpenApi\Annotations',
                    ],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [
                        'first',
                        'second',
                    ],
                    'properties' => [],
                ],
            ],
        ];

        yield 'namespaces1' => [
            'PHP/namespaces1.php',
            [
                'Foo\\FooClass' => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [],
                    'properties' => [],
                ],
                'Bar\\BarClass' => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [],
                    'properties' => [],
                ],
            ],
        ];

        yield 'namespaces2' => [
            'PHP/namespaces2.php',
            [
                'Foo\\FooClass' => [
                    'uses' => [],
                    'interfaces' => [],
                    'enums' => [],
                    'traits' => [],
                    'methods' => [],
                    'properties' => [],
                ],
                'Bar\\BarClass' => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [],
                    'properties' => [],
                ],
            ],
        ];

        yield 'namespaces3' => [
            'PHP/namespaces3.php',
            [
                '\\BarClass' => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [],
                    'properties' => [],
                ],
            ],
        ];

        if (\PHP_VERSION_ID >= 80100) {
            yield 'enum' => [
                'PHP/Enums/StatusEnum.php',
                [
                    'OpenApi\\Tests\\Fixtures\\PHP\\Enums\\StatusEnum' => [
                        'uses' => [
                            'OAT' => 'OpenApi\\Attributes',
                        ],
                        'interfaces' => [],
                        'enums' => [],
                        'traits' => [],
                        'methods' => [],
                        'properties' => [],
                    ],
                ],
            ];

            yield 'enum-backed' => [
                'PHP/Enums/StatusEnumBacked.php',
                [
                    'OpenApi\\Tests\\Fixtures\\PHP\\Enums\\StatusEnumBacked' => [
                        'uses' => [
                            'OAT' => 'OpenApi\\Attributes',
                        ],
                        'interfaces' => [],
                        'enums' => [],
                        'traits' => [],
                        'methods' => [],
                        'properties' => [],
                    ],
                ],
            ];
        }
    }

    /**
     * @dataProvider scanCases
     */
    public function testScanFile(string $fixture, array $expected): void
    {
        $result = (new TokenScanner())->scanFile($this->fixture($fixture));
        $this->assertEquals($expected, $result);
    }
}
