<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Utils;

use OpenApi\Tests\Fixtures\PHP\BracedTrait;
use OpenApi\Tests\Fixtures\PHP\BracesInStrings;
use OpenApi\Tests\Fixtures\PHP\EdgeEnum;
use OpenApi\Tests\Fixtures\PHP\EdgeLabel;
use OpenApi\Tests\Fixtures\PHP\EdgeOneLine;
use OpenApi\Tests\Fixtures\PHP\EdgeOnOneLine;
use OpenApi\Tests\Fixtures\PHP\EdgeOther;
use OpenApi\Tests\Fixtures\PHP\EdgeOuter;
use OpenApi\Tests\Fixtures\PHP\OwnMembers;
use OpenApi\Tests\Fixtures\PHP\SharedMembers;
use OpenApi\Tests\Fixtures\PHP\TraitEdges;
use OpenApi\Tests\OpenApiTestCase;
use OpenApi\Utils\ClassReflector;
use OpenApi\Utils\SourceInspector;
use OpenApi\Utils\TokenScanner;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * `SourceInspector` finds the classes `TokenScanner` finds in every PHP file the tests and the
 * examples hold, and answers as it does for the PHP syntax fixtures.
 */
final class SourceInspectorTest extends OpenApiTestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function files(): iterable
    {
        $root = dirname(__DIR__, 2);
        foreach (['tests/Fixtures', 'docs/examples'] as $directory) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator("{$root}/{$directory}", \FilesystemIterator::SKIP_DOTS));
            foreach ($files as $file) {
                if ($file instanceof \SplFileInfo && $file->getExtension() === 'php') {
                    yield substr($file->getPathname(), strlen($root) + 1) => [$file->getPathname()];
                }
            }
        }
    }

    #[DataProvider('files')]
    public function testFindsTheClassesTheParserFinds(string $file): void
    {
        $parsed = $this->parsed($file);

        $this->assertSame($parsed, (new SourceInspector())->classesIn($file));
    }

    /**
     * The PHP syntax fixtures that load. Loading every fixture would declare some classes twice.
     *
     * @return iterable<string, array{string}>
     */
    public static function declarations(): iterable
    {
        $fixtures = [
            'AnonymousClasses', 'AnonymousFunctions', 'BracesInStrings', 'AttributeSyntax', 'CurlyBraceAccess',
            'Enums/StatusEnum', 'Enums/StatusEnumBacked', 'Inheritance/ExtendsBaseInterface', 'Inheritance/ExtendsClass',
            'MultiNamespace', 'NamedArguments', 'NamespacesBraced', 'NamespacesSequential', 'NoNamespace', 'OwnMembers',
            'PromotedProperties', 'ReadonlyAndUnionTypes', 'References', 'TraitEdges', 'TraitUsage', 'TypedConstants',
        ];
        foreach ($fixtures as $fixture) {
            yield $fixture => [dirname(__DIR__) . "/Fixtures/PHP/{$fixture}.php"];
        }
    }

    #[DataProvider('declarations')]
    public function testAnswersAsTheParserDoes(string $file): void
    {
        $scanner = new TokenScanner();
        $inspector = new SourceInspector();
        $names = static fn (array $traits): array => array_map(static fn (\ReflectionClass $trait): string => $trait->getName(), $traits);

        // the traits live in the file of the class that uses them
        class_exists(TraitEdges::class);
        class_exists(BracesInStrings::class);
        $compared = 0;
        foreach ($this->parsed($file) as $name) {
            [$class] = ClassReflector::tryReflect($name);
            if (!$class instanceof \ReflectionClass || $class->getFileName() !== realpath($file)) {
                continue;
            }

            $this->assertSame($names($scanner->directTraits($class)), $names($inspector->directTraits($class)), "{$name} traits");
            foreach ([...$class->getMethods(), ...$class->getProperties(), ...$class->getReflectionConstants()] as $member) {
                if ($member instanceof \ReflectionProperty && $member->isPromoted()) {
                    // the parser misses those a trait promotes
                    continue;
                }

                $this->assertSame(
                    $scanner->declares($class, $member),
                    $inspector->declares($class, $member),
                    "{$name}::{$member->getName()}",
                );
            }
            ++$compared;
        }

        $this->addToAssertionCount($compared === 0 ? 1 : 0);
    }

    /**
     * @return iterable<string, array{class-string, array{methods: list<string>, properties: list<string>, consts: list<string>}, list<string>}>
     */
    public static function bodies(): iterable
    {
        yield 'own, inherited and trait members' => [
            OwnMembers::class,
            ['methods' => ['own'], 'properties' => ['own'], 'consts' => ['OWN']],
            [SharedMembers::class],
        ];
        yield 'aliases, conflicts and redeclarations' => [
            TraitEdges::class,
            [
                'methods' => ['overridden', 'implemented'],
                'properties' => ['redeclared', 'redeclaredWithDoc', 'redeclaredWithAttribute', 'redeclaredWithOtherAttribute'],
                'consts' => ['REDECLARED'],
            ],
            [EdgeOuter::class, EdgeOther::class],
        ];
        yield 'braces in strings' => [
            BracesInStrings::class,
            ['methods' => ['json'], 'properties' => ['after'], 'consts' => []],
            [BracedTrait::class],
        ];
        yield "a trait on the class's line" => [
            EdgeOnOneLine::class,
            ['methods' => [], 'properties' => [], 'consts' => []],
            [EdgeOneLine::class],
        ];
        yield 'an enum with a trait' => [
            EdgeEnum::class,
            ['methods' => [], 'properties' => [], 'consts' => []],
            [EdgeLabel::class],
        ];
    }

    /**
     * A class declares what its body holds, and nothing it inherits or takes from a trait.
     *
     * @param class-string                                                                 $name
     * @param array{methods: list<string>, properties: list<string>, consts: list<string>} $declared
     * @param list<string>                                                                 $traits
     */
    #[DataProvider('bodies')]
    public function testDeclaresWhatTheClassBodyHolds(string $name, array $declared, array $traits): void
    {
        // the traits live in the file of the class that uses them
        class_exists(OwnMembers::class);
        class_exists(TraitEdges::class);
        class_exists(BracesInStrings::class);
        $inspector = new SourceInspector();
        $class = new \ReflectionClass($name);
        $own = static fn (array $members): array => array_values(array_map(
            static fn (\ReflectionMethod|\ReflectionProperty|\ReflectionClassConstant $member): string => $member->getName(),
            array_filter($members, static fn (\ReflectionMethod|\ReflectionProperty|\ReflectionClassConstant $member): bool => $inspector->declares($class, $member)),
        ));

        $this->assertSame($declared, [
            'methods' => $own($class->getMethods()),
            'properties' => $own($class->getProperties()),
            'consts' => $own($class->getReflectionConstants()),
        ]);
        $this->assertSame($traits, array_map(static fn (\ReflectionClass $trait): string => $trait->getName(), $inspector->directTraits($class)));
    }

    public function testANamespaceClosedByATagHoldsTheClassesAfterIt(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'inspector');
        file_put_contents($file, "<?php\n\nnamespace Closed ?>\n<?php\n\nclass AfterTheTag\n{\n}\n");

        try {
            $this->assertSame(['Closed\\AfterTheTag'], (new SourceInspector())->classesIn($file));
        } finally {
            unlink($file);
        }
    }

    /**
     * Code from `eval()` has no file to parse, and reflection still tells a trait's members apart.
     */
    public function testAnEvaluatedClassTellsItsOwnMembersFromATraits(): void
    {
        // the trait lives in the file of the class that uses it
        class_exists(OwnMembers::class);
        static $instance = null;
        $instance ??= eval('namespace OpenApi\\Tests\\Utils; class EvaluatedWithTrait { use \\OpenApi\\Tests\\Fixtures\\PHP\\SharedMembers; public $own; public function own() {} } return new EvaluatedWithTrait();');
        $inspector = new SourceInspector();
        $class = new \ReflectionClass($instance);

        $this->assertTrue($inspector->declares($class, $class->getProperty('own')));
        $this->assertTrue($inspector->declares($class, $class->getMethod('own')));
        $this->assertFalse($inspector->declares($class, $class->getProperty('shared')));
        $this->assertFalse($inspector->declares($class, $class->getMethod('shared')));
        $this->assertFalse($inspector->declares($class, $class->getReflectionConstant('SHARED') ?: throw new \LogicException()));
        $this->assertSame([SharedMembers::class], array_map(static fn (\ReflectionClass $trait): string => $trait->getName(), $inspector->directTraits($class)));
    }

    /**
     * The classes the parser finds in a file, by their names, or none where it cannot parse it.
     *
     * @return list<string>
     */
    private function parsed(string $file): array
    {
        try {
            $classes = array_keys((new TokenScanner())->scanFile($file));
        } catch (\RuntimeException) {
            $this->markTestSkipped('the parser cannot read it');
        }

        return array_map(static fn (string $class): string => ltrim($class, '\\'), $classes);
    }
}
