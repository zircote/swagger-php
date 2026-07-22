<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Utils;

use OpenApi\Tests\OpenApiTestCase;
use OpenApi\Utils\TokenScanner;
use PHPUnit\Framework\Attributes\DataProvider;

final class TokenScannerTest extends OpenApiTestCase
{
    public static function scanCases(): iterable
    {
        yield 'abstract' => [
            'PHP/AbstractKeyword.php',
            [
                'OpenApi\Tests\Fixtures\PHP\AbstractKeyword' => [
                    'uses' => [
                        'OAT' => 'OpenApi\\Attributes',
                    ],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['stuff', 'other', 'another'],
                    'properties' => [],
                    'consts' => [],
                ],
            ],
        ];

        yield 'references' => [
            'PHP/References.php',
            [
                'OpenApi\Tests\Fixtures\PHP\References' => [
                    'uses' => [
                        'OAT' => 'OpenApi\\Attributes',
                    ],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [
                        'return_ref',
                    ],
                    'properties' => [],
                    'consts' => [],
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
                'OpenApi\Tests\Fixtures\PHP\MethodAttr' => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [],
                    'properties' => [],
                    'consts' => [],
                ],
                'OpenApi\Tests\Fixtures\PHP\GenericAttr' => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['__construct'],
                    'properties' => [],
                    'consts' => [],
                ],
                'OpenApi\Tests\Fixtures\PHP\Decorated' => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['foo', 'bar'],
                    'properties' => [],
                    'consts' => [],
                ],
            ],
        ];

        yield 'ExtendsClass' => [
            'PHP/Inheritance/ExtendsClass.php',
            [
                'OpenApi\Tests\Fixtures\PHP\Inheritance\ExtendsClass' => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['extendsClassFunc'],
                    'properties' => ['extendsClassProp'],
                    'consts' => [],
                ],
            ],
        ];

        yield 'ExtendsInterface' => [
            'PHP/Inheritance/ExtenedsBaseInterface.php',
            [
                'OpenApi\Tests\Fixtures\PHP\Inheritance\ExtenedsBaseInterface' => [
                    'uses' => [],
                    'interfaces' => ['OpenApi\Tests\Fixtures\PHP\Inheritance\BaseInterface'],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [],
                    'properties' => [],
                    'consts' => [],
                ],
            ],
        ];

        yield 'CustomerInterface' => [
            'CustomerInterface.php',
            [
                'OpenApi\Tests\Fixtures\CustomerInterface' => [
                    'uses' => ['OAT' => 'OpenApi\\Attributes'],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['firstname', 'secondname', 'thirdname', 'fourthname', 'lastname', 'tags', 'submittedBy', 'friends', 'bestFriend'],
                    'properties' => [],
                    'consts' => [],
                ],
            ],
        ];

        yield 'AllTraits' => [
            'Parser/AllTraits.php',
            [
                'OpenApi\Tests\Fixtures\Parser\AllTraits' => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [
                        'OpenApi\Tests\Fixtures\Parser\AsTrait',
                        'OpenApi\Tests\Fixtures\Parser\HelloTrait',
                    ],
                    'enums' => [],
                    'methods' => [],
                    'properties' => [],
                    'consts' => [],
                ],
            ],
        ];

        yield 'User' => [
            'Parser/User.php',
            [
                'OpenApi\Tests\Fixtures\Parser\User' => [
                    'uses' => [
                        'Hello' => 'OpenApi\Tests\Fixtures\Parser\HelloTrait',
                        'ParentClass' => 'OpenApi\Tests\Fixtures\Parser\Sub\SubClass',
                        'OA' => 'OpenApi\\Annotations',
                    ],
                    'interfaces' => ['OpenApi\Tests\Fixtures\Parser\UserInterface'],
                    'traits' => ['OpenApi\Tests\Fixtures\Parser\HelloTrait'],
                    'enums' => [],
                    'methods' => ['getFirstName'],
                    'properties' => [],
                    'consts' => [
                        'CONSTANT',
                    ],
                ],
            ],
        ];

        yield 'HelloTrait' => [
            'Parser/HelloTrait.php',
            [
                'OpenApi\Tests\Fixtures\Parser\HelloTrait' => [
                    'uses' => [
                        'Aliased' => 'OpenApi\Tests\Fixtures\Parser\AsTrait',
                        'OAT' => 'OpenApi\\Attributes',
                    ],
                    'interfaces' => [],
                    'traits' => [
                        'OpenApi\Tests\Fixtures\Parser\OtherTrait',
                        'OpenApi\Tests\Fixtures\Parser\AsTrait',
                    ],
                    'enums' => [],
                    'methods' => [],
                    'properties' => ['greet'],
                    'consts' => [],
                ],
            ],
        ];

        yield 'Php8PromotedProperties' => [
            'PHP/Php8PromotedProperties.php',
            [
                \OpenApi\Tests\Fixtures\PHP\Php8PromotedProperties::class => [
                    'uses' => [
                        'OAT' => 'OpenApi\\Attributes',
                        'OA' => 'OpenApi\\Annotations',
                    ],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['__construct'],
                    'properties' => ['labels', 'tags', 'id'],
                    'consts' => [],
                ],
            ],
        ];

        yield 'Php8NamedArguments' => [
            'PHP/Php8NamedArguments.php',
            [
                \OpenApi\Tests\Fixtures\PHP\Php8NamedArguments::class => [
                    'uses' => [
                        'OAT' => 'OpenApi\\Attributes',
                    ],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['useFoo', 'foo'],
                    'properties' => [],
                    'consts' => [],
                ],
                'OpenApi\Tests\Fixtures\PHP\ReservedWordsAttr' => [
                    'uses' => [
                        'OAT' => 'OpenApi\\Attributes',
                    ],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['__construct'],
                    'properties' => [],
                    'consts' => [],
                ],
                'OpenApi\Tests\Fixtures\PHP\UserlandClass' => [
                    'uses' => [
                        'OAT' => 'OpenApi\\Attributes',
                    ],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [],
                    'consts' => [],
                    'properties' => [],
                ],
            ],
        ];

        yield 'AnonymousFunctions' => [
            'PHP/AnonymousFunctions.php',
            [
                'OpenApi\Tests\Fixtures\PHP\AnonymousFunctions' => [
                    'uses' => [
                        'OAT' => 'OpenApi\\Attributes',
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
                    'consts' => [],
                ],
            ],
        ];

        yield 'CurlyBrace' => [
            'PHP/MultipleFunctions.php',
            [
                'OpenApi\Tests\Fixtures\PHP\MultipleFunctions' => [
                    'uses' => [
                        'OAT' => 'OpenApi\\Attributes',
                    ],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [
                        'first',
                        'second',
                    ],
                    'properties' => [],
                    'consts' => [],
                ],
            ],
        ];

        yield 'namespaces1' => [
            'PHP/namespaces1.php',
            [
                'Foo\FooClass' => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [],
                    'properties' => [],
                    'consts' => [],
                ],
                'Bar\BarClass' => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [],
                    'properties' => [],
                    'consts' => [],
                ],
            ],
        ];

        yield 'namespaces2' => [
            'PHP/namespaces2.php',
            [
                'Foo\FooClass' => [
                    'uses' => [],
                    'interfaces' => [],
                    'enums' => [],
                    'traits' => [],
                    'methods' => [],
                    'properties' => [],
                    'consts' => [],
                ],
                'Bar\BarClass' => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [],
                    'properties' => [],
                    'consts' => [],
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
                    'consts' => [],
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
                    'consts' => [],
                ],
                'OpenApi\Tests\Fixtures\PHP\MultiNamespace' => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => [],
                    'properties' => [],
                    'consts' => [],
                ],
            ],
        ];

        yield 'enum' => [
            'PHP/Enums/StatusEnum.php',
            [
                'OpenApi\Tests\Fixtures\PHP\Enums\StatusEnum' => [
                    'uses' => [
                        'OAT' => 'OpenApi\\Attributes',
                    ],
                    'interfaces' => [],
                    'enums' => [],
                    'traits' => [],
                    'methods' => [],
                    'properties' => [],
                    'consts' => [],
                ],
            ],
        ];

        yield 'enum-backed' => [
            'PHP/Enums/StatusEnumBacked.php',
            [
                'OpenApi\Tests\Fixtures\PHP\Enums\StatusEnumBacked' => [
                    'uses' => [
                        'OAT' => 'OpenApi\\Attributes',
                    ],
                    'interfaces' => [],
                    'enums' => [],
                    'traits' => [],
                    'methods' => [],
                    'properties' => [],
                    'consts' => [],
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

    public function testScanFileCachesResults(): void
    {
        $scanner = new TokenScanner();
        $filename = $this->fixture('PHP/AbstractKeyword.php');

        $first = $scanner->scanFile($filename);
        $second = $scanner->scanFile($filename);

        $this->assertSame($first, $second);
    }

    public function testDetailsForReturnsMatchingEntry(): void
    {
        $scanner = new TokenScanner();
        $rc = new \ReflectionClass(\OpenApi\Tests\Fixtures\PHP\Inheritance\ExtendsClass::class);

        $details = $scanner->detailsFor($rc);

        $this->assertNotNull($details);
        $this->assertSame(['extendsClassProp'], $details['properties']);
        $this->assertSame(['extendsClassFunc'], $details['methods']);
    }

    public function testDetailsForReturnsNullForInternalClass(): void
    {
        $scanner = new TokenScanner();
        $rc = new \ReflectionClass(\stdClass::class);

        $this->assertNull($scanner->detailsFor($rc));
    }
}
