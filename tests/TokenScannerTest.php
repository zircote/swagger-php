<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\TokenScanner;
use PHPUnit\Framework\Attributes\DataProvider;

final class TokenScannerTest extends OpenApiTestCase
{
    public static function scanCases(): iterable
    {
        yield 'abstract' => [
            'PHP/AbstractKeyword.php',
            [
                \OpenApi\Tests\Fixtures\PHP\AbstractKeyword::class => [
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

        yield 'references' => [
            'PHP/References.php',
            [
                \OpenApi\Tests\Fixtures\PHP\References::class => [
                    'uses' => [
                        'OAT' => 'OpenApi\Attributes',
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
                \OpenApi\Tests\Fixtures\PHP\MethodAttr::class => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [],
                    'properties' => [],
                ],
                \OpenApi\Tests\Fixtures\PHP\GenericAttr::class => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['__construct'],
                    'properties' => [],
                ],
                \OpenApi\Tests\Fixtures\PHP\Decorated::class => [
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
                \OpenApi\Tests\Fixtures\PHP\Inheritance\ExtendsClass::class => [
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
                \OpenApi\Tests\Fixtures\PHP\Inheritance\ExtenedsBaseInterface::class => [
                    'uses' => [],
                    'interfaces' => [\OpenApi\Tests\Fixtures\PHP\Inheritance\BaseInterface::class],
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
                \OpenApi\Tests\Fixtures\CustomerInterface::class => [
                    'uses' => ['OAT' => 'OpenApi\\Attributes'],
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
                \OpenApi\Tests\Fixtures\Parser\AllTraits::class => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [
                        \OpenApi\Tests\Fixtures\Parser\AsTrait::class,
                        \OpenApi\Tests\Fixtures\Parser\HelloTrait::class,
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
                \OpenApi\Tests\Fixtures\Parser\User::class => [
                    'uses' => [
                        'Hello' => \OpenApi\Tests\Fixtures\Parser\HelloTrait::class,
                        'ParentClass' => \OpenApi\Tests\Fixtures\Parser\Sub\SubClass::class,
                        'OA' => 'OpenApi\Annotations',
                    ],
                    'interfaces' => [\OpenApi\Tests\Fixtures\Parser\UserInterface::class],
                    'traits' => [\OpenApi\Tests\Fixtures\Parser\HelloTrait::class],
                    'enums' => [],
                    'methods' => ['getFirstName'],
                    'properties' => [],
                ],
            ],
        ];

        yield 'HelloTrait' => [
            'Parser/HelloTrait.php',
            [
                \OpenApi\Tests\Fixtures\Parser\HelloTrait::class => [
                    'uses' => [
                        'Aliased' => \OpenApi\Tests\Fixtures\Parser\AsTrait::class,
                        'OAT' => 'OpenApi\\Attributes',
                    ],
                    'interfaces' => [],
                    'traits' => [
                        \OpenApi\Tests\Fixtures\Parser\OtherTrait::class,
                        \OpenApi\Tests\Fixtures\Parser\AsTrait::class,
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
                \OpenApi\Tests\Fixtures\PHP\Php8PromotedProperties::class => [
                    'uses' => [
                        'OAT' => 'OpenApi\\Attributes',
                        'OA' => 'OpenApi\Annotations',
                    ],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['__construct'],
                    'properties' => ['labels', 'tags', 'id'],
                ],
            ],
        ];

        yield 'Php8NamedArguments' => [
            'PHP/Php8NamedArguments.php',
            [
                \OpenApi\Tests\Fixtures\PHP\Php8NamedArguments::class => [
                    'uses' => [
                        'OAT' => 'OpenApi\Attributes',
                    ],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['useFoo', 'foo'],
                    'properties' => [],
                ],
                \OpenApi\Tests\Fixtures\PHP\ReservedWordsAttr::class => [
                    'uses' => [
                        'OAT' => 'OpenApi\Attributes',
                    ],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['__construct'],
                    'properties' => [],
                ],
                \OpenApi\Tests\Fixtures\PHP\UserlandClass::class => [
                    'uses' => [
                        'OAT' => 'OpenApi\Attributes',
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
                \OpenApi\Tests\Fixtures\PHP\AnonymousFunctions::class => [
                    'uses' => [
                        'OAT' => 'OpenApi\Attributes',
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
                \OpenApi\Tests\Fixtures\PHP\MultipleFunctions::class => [
                    'uses' => [
                        'OAT' => 'OpenApi\Attributes',
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
                \Foo\FooClass::class => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [],
                    'properties' => [],
                ],
                \Bar\BarClass::class => [
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
                \Foo\FooClass::class => [
                    'uses' => [],
                    'interfaces' => [],
                    'enums' => [],
                    'traits' => [],
                    'methods' => [],
                    'properties' => [],
                ],
                \Bar\BarClass::class => [
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

        yield 'MultiNamespace' => [
            'PHP/MultiNamespace.php',
            [
                '\\Test' => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [],
                    'properties' => [],
                ],
                \OpenApi\Tests\Fixtures\PHP\MultiNamespace::class => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [],
                    'properties' => [],
                ],
            ],
        ];

        yield 'enum' => [
            'PHP/Enums/StatusEnum.php',
            [
                \OpenApi\Tests\Fixtures\PHP\Enums\StatusEnum::class => [
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
                \OpenApi\Tests\Fixtures\PHP\Enums\StatusEnumBacked::class => [
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

    #[DataProvider('scanCases')]
    public function testScanFile(string $fixture, array $expected): void
    {
        $result = (new TokenScanner())->scanFile($this->fixture($fixture));
        $this->assertEquals($expected, $result);
    }
}
