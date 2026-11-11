<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Utils;

use OpenApi\Tests\Fixtures\PHP\GroupedImports\Inline;
use OpenApi\Tests\Fixtures\PHP\GroupedImports\MixedKinds;
use OpenApi\Tests\Fixtures\PHP\GroupedImports\Multiline;
use OpenApi\Tests\Fixtures\PHP\Inheritance\BaseClass;
use OpenApi\Tests\Fixtures\PHP\Inheritance\BaseInterface;
use OpenApi\Tests\Fixtures\PHP\Inheritance\BaseTrait;
use OpenApi\Tests\Fixtures\PHP\Inheritance\ExtendsClass;
use OpenApi\Tests\OpenApiTestCase;
use OpenApi\Utils\TokenScanner;
use PHPUnit\Framework\Attributes\DataProvider;

final class TokenScannerTest extends OpenApiTestCase
{
    /**
     * @return iterable<mixed>
     */
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

        yield 'anonymous classes' => [
            'PHP/AnonymousClasses.php',
            [],
        ];

        yield 'attribute syntax' => [
            'PHP/AttributeSyntax.php',
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

        yield 'readonly and union types' => [
            'PHP/ReadonlyAndUnionTypes.php',
            [
                'OpenApi\Tests\Fixtures\PHP\ReadonlyAndUnionTypes' => [
                    'uses' => [],
                    // the anonymous class in the constructor implements both, and must not
                    // contribute to the enclosing class
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    'methods' => ['__construct', 'never'],
                    'properties' => ['alwaysTrue', 'falseOrInt', 'dnf', 'intersection', 'alwaysNull'],
                    'consts' => [],
                ],
            ],
        ];

        yield 'typed constants' => [
            'PHP/TypedConstants.php',
            [
                'OpenApi\Tests\Fixtures\PHP\TypedConstants' => [
                    'uses' => [],
                    'interfaces' => [],
                    'traits' => [],
                    'enums' => [],
                    // `self::{$which}` is a fetch, not a declaration
                    'methods' => ['fetch'],
                    'properties' => [],
                    'consts' => ['NAME', 'COUNT', 'TAGS', 'CONTRACT'],
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
            'PHP/Inheritance/ExtendsBaseInterface.php',
            [
                'OpenApi\Tests\Fixtures\PHP\Inheritance\ExtendsBaseInterface' => [
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

        yield 'trait usage' => [
            'PHP/TraitUsage.php',
            [
                'OpenApi\Tests\Fixtures\PHP\AlphaTrait' => self::traitUsageImports(),
                'OpenApi\Tests\Fixtures\PHP\BetaTrait' => self::traitUsageImports(),
                'OpenApi\Tests\Fixtures\PHP\GammaTrait' => self::traitUsageImports(),
                'OpenApi\Tests\Fixtures\PHP\CombinedTrait' => self::traitUsageImports([
                    'traits' => [
                        'OpenApi\Tests\Fixtures\PHP\AlphaTrait',
                        'OpenApi\Tests\Fixtures\PHP\BetaTrait',
                    ],
                ]),
                'OpenApi\Tests\Fixtures\PHP\TraitUsage' => self::traitUsageImports([
                    // both resolved through the aliases the file imports them under
                    'interfaces' => ['OpenApi\Tests\Fixtures\PHP\Inheritance\BaseInterface'],
                    'traits' => [
                        'OpenApi\Tests\Fixtures\PHP\CombinedTrait',
                        'OpenApi\Tests\Fixtures\PHP\GammaTrait',
                    ],
                    'methods' => ['method'],
                    'properties' => ['name'],
                    'consts' => ['CONSTANT'],
                ]),
            ],
        ];

        yield 'PromotedProperties' => [
            'PHP/PromotedProperties.php',
            [
                'OpenApi\Tests\Fixtures\PHP\PromotedProperties' => [
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

        yield 'NamedArguments' => [
            'PHP/NamedArguments.php',
            [
                'OpenApi\Tests\Fixtures\PHP\NamedArguments' => [
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
                        'other',
                        'shortFn',
                        'staticShortFn',
                        'withUse',
                        'dollarCurly1',
                        'dollarCurly2',
                        'curlyOpen',
                        'query',
                    ],
                    'properties' => [],
                    'consts' => [],
                ],
            ],
        ];

        yield 'curly brace property access' => [
            'PHP/CurlyBraceAccess.php',
            [
                'OpenApi\Tests\Fixtures\PHP\CurlyBraceAccess' => [
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

        yield 'namespaces declared sequentially' => [
            'PHP/NamespacesSequential.php',
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

        yield 'namespaces declared with braces' => [
            'PHP/NamespacesBraced.php',
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

        yield 'no namespace' => [
            'PHP/NoNamespace.php',
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

    /**
     * @param array<mixed> $expected
     */
    #[DataProvider('scanCases')]
    public function testScanFile(string $fixture, array $expected): void
    {
        $result = (new TokenScanner())->scanFile($this->fixture($fixture));
        $this->assertEquals($expected, $result);
    }

    /**
     * @return iterable<string, array{string, class-string}>
     */
    public static function groupedImportCases(): iterable
    {
        yield 'inline group' => ['PHP/GroupedImports/Inline.php', Inline::class];
        yield 'multiline group' => ['PHP/GroupedImports/Multiline.php', Multiline::class];
        yield 'mixed class, function and constant groups' => ['PHP/GroupedImports/MixedKinds.php', MixedKinds::class];
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('groupedImportCases')]
    public function testGroupedImportsResolveAliasesAndTraits(string $fixture, string $class): void
    {
        $scanner = new TokenScanner();
        $details = $scanner->scanFile($this->fixture($fixture))[$class];

        $this->assertSame([
            'ParentClass' => BaseClass::class,
            'Contract' => BaseInterface::class,
            'Shared' => BaseTrait::class,
        ], $details['uses']);
        $this->assertSame([BaseInterface::class], $details['interfaces']);
        $this->assertSame([BaseTrait::class], $details['traits']);
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
        $rc = new \ReflectionClass(ExtendsClass::class);

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

    public function testDetailsForReturnsNullForAClassFromEval(): void
    {
        static $instance = null;
        $instance ??= eval('namespace OpenApi\Tests\Utils; class EvaluatedForDetails {} return new EvaluatedForDetails();');

        $this->assertNull((new TokenScanner())->detailsFor(new \ReflectionClass($instance)));
    }

    public function testDetailsForDoesNotRecheckAScannedFile(): void
    {
        $class = 'ScannedOnce' . bin2hex(random_bytes(4));
        $file = sys_get_temp_dir() . "/{$class}.php";
        file_put_contents($file, "<?php\n\nnamespace OpenApi\\Tests\\Utils;\n\nfinal class {$class}\n{\n    public \$prop;\n}\n");
        require $file;
        $class = "OpenApi\\Tests\\Utils\\{$class}";
        $this->assertTrue(class_exists($class));
        $scanner = new TokenScanner();
        $rc = new \ReflectionClass($class);

        $first = $scanner->detailsFor($rc);
        unlink($file);

        $this->assertSame(['prop'], $first['properties'] ?? null);
        $this->assertSame($first, $scanner->detailsFor($rc));
    }

    /**
     * @param  array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    protected static function traitUsageImports(array $overrides = []): array
    {
        return $overrides + [
            'uses' => [
                'ParentClass' => 'OpenApi\\Tests\\Fixtures\\PHP\\Inheritance\\BaseClass',
                'Contract' => 'OpenApi\\Tests\\Fixtures\\PHP\\Inheritance\\BaseInterface',
            ],
            'interfaces' => [],
            'traits' => [],
            'enums' => [],
            'methods' => [],
            'properties' => [],
            'consts' => [],
        ];
    }
}
