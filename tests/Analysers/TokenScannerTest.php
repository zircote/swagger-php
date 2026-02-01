<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Analysers;

use OpenApi\Analysers\TokenScanner;
use OpenApi\Tests\OpenApiTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Bar\BarClass;
use Foo\FooClass;
use OpenApi\Tests\Fixtures\CustomerInterface;
use OpenApi\Tests\Fixtures\PHP\AbstractKeyword;
use OpenApi\Tests\Fixtures\PHP\AnonymousFunctions;
use OpenApi\Tests\Fixtures\PHP\Decorated;
use OpenApi\Tests\Fixtures\PHP\Enums\StatusEnum;
use OpenApi\Tests\Fixtures\PHP\Enums\StatusEnumBacked;
use OpenApi\Tests\Fixtures\PHP\GenericAttr;
use OpenApi\Tests\Fixtures\PHP\Inheritance\BaseInterface;
use OpenApi\Tests\Fixtures\PHP\Inheritance\ExtendsClass;
use OpenApi\Tests\Fixtures\PHP\Inheritance\ExtenedsBaseInterface;
use OpenApi\Tests\Fixtures\PHP\MethodAttr;
use OpenApi\Tests\Fixtures\PHP\MultiNamespace;
use OpenApi\Tests\Fixtures\PHP\MultipleFunctions;
use OpenApi\Tests\Fixtures\PHP\Php8NamedArguments;
use OpenApi\Tests\Fixtures\PHP\Php8PromotedProperties;
use OpenApi\Tests\Fixtures\PHP\References;
use OpenApi\Tests\Fixtures\PHP\ReservedWordsAttr;
use OpenApi\Tests\Fixtures\PHP\UserlandClass;
use OpenApi\Tests\Fixtures\Parser\AllTraits;
use OpenApi\Tests\Fixtures\Parser\AsTrait;
use OpenApi\Tests\Fixtures\Parser\HelloTrait;
use OpenApi\Tests\Fixtures\Parser\OtherTrait;
use OpenApi\Tests\Fixtures\Parser\Sub\SubClass;
use OpenApi\Tests\Fixtures\Parser\User;
use OpenApi\Tests\Fixtures\Parser\UserInterface;

final class TokenScannerTest extends OpenApiTestCase
{
    public static function scanCases(): iterable
    {
        yield 'abstract' => [
            'PHP/AbstractKeyword.php',
            [
                AbstractKeyword::class => [
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
                References::class => [
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
                MethodAttr::class => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [],
                    'properties' => [],
                ],
                GenericAttr::class => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['__construct'],
                    'properties' => [],
                ],
                Decorated::class => [
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
                ExtendsClass::class => [
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
                ExtenedsBaseInterface::class => [
                    'uses' => [],
                    'interfaces' => [BaseInterface::class],
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
                CustomerInterface::class => [
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
                AllTraits::class => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [
                        AsTrait::class,
                        HelloTrait::class,
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
                User::class => [
                    'uses' => [
                        'Hello' => HelloTrait::class,
                        'ParentClass' => SubClass::class,
                        'OA' => 'OpenApi\Annotations',
                    ],
                    'interfaces' => [UserInterface::class],
                    'traits' => [HelloTrait::class],
                    'enums' => [],
                    'methods' => ['getFirstName'],
                    'properties' => [],
                ],
            ],
        ];

        yield 'HelloTrait' => [
            'Parser/HelloTrait.php',
            [
                HelloTrait::class => [
                    'uses' => [
                        'Aliased' => AsTrait::class,
                        'OAT' => 'OpenApi\\Attributes',
                    ],
                    'interfaces' => [],
                    'traits' => [
                        OtherTrait::class,
                        AsTrait::class,
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
                Php8PromotedProperties::class => [
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
                Php8NamedArguments::class => [
                    'uses' => [
                        'OAT' => 'OpenApi\Attributes',
                    ],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['useFoo', 'foo'],
                    'properties' => [],
                ],
                ReservedWordsAttr::class => [
                    'uses' => [
                        'OAT' => 'OpenApi\Attributes',
                    ],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['__construct'],
                    'properties' => [],
                ],
                UserlandClass::class => [
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
                AnonymousFunctions::class => [
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
                MultipleFunctions::class => [
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
                FooClass::class => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [],
                    'properties' => [],
                ],
                BarClass::class => [
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
                FooClass::class => [
                    'uses' => [],
                    'interfaces' => [],
                    'enums' => [],
                    'traits' => [],
                    'methods' => [],
                    'properties' => [],
                ],
                BarClass::class => [
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
                MultiNamespace::class => [
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
                StatusEnum::class => [
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
                StatusEnumBacked::class => [
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
