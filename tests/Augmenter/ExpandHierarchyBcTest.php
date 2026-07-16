<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Assembler;
use OpenApi\Augmenter;
use OpenApi\Builder;
use OpenApi\Compiler\OpenApi31Compiler;
use OpenApi\Tests\Concerns\AssertsSchemaStructure;
use OpenApi\Utils\TokenScanner;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Verifies that both spec and classic pipelines produce the same structural
 * composition (allOf refs + property names) for identical class hierarchies.
 */
final class ExpandHierarchyBcTest extends TestCase
{
    use AssertsSchemaStructure;

    private const EXPECTED_FILE = __DIR__ . '/../Fixtures/Augmenter/Hierarchy/expected.yaml';

    #[DataProvider('pipelines')]
    public function testMatchesExpected(string $pipeline, array $schemas): void
    {
        $this->assertCompiledSchemasMatchFile($schemas, self::EXPECTED_FILE, $pipeline);
    }

    public static function pipelines(): iterable
    {
        yield 'spec' => ['spec', self::buildSpec(__DIR__ . '/../Fixtures/Augmenter/Hierarchy/Spec')];
        yield 'classic' => ['classic', self::buildClassic(__DIR__ . '/../Fixtures/Augmenter/Hierarchy/Classic')];
    }

    protected static function buildSpec(string $directory): array
    {
        $tokenScanner = new TokenScanner();
        $assembler = new Assembler();

        foreach (glob($directory . '/*.php') as $file) {
            require_once $file;
            foreach (array_keys($tokenScanner->scanFile($file)) as $class) {
                if (class_exists($class) || interface_exists($class) || enum_exists($class) || trait_exists($class)) {
                    $assembler->collect(new \ReflectionClass($class));
                }
            }
        }

        $specification = $assembler->getSpecification();

        (new Augmenter\ExpandHierarchy())($specification);
        (new Augmenter\InferNames())($specification);
        (new Augmenter\Type())($specification);
        (new Augmenter\Refs())($specification);

        $compiler = new OpenApi31Compiler();
        $output = $compiler->compile($specification);

        return $output['components']['schemas'] ?? [];
    }

    protected static function buildClassic(string $directory): array
    {
        $result = (new Builder())
            ->setMode(Builder\Mode::CLASSIC)
            ->addSource($directory)
            ->setVersion('3.1.0')
            ->build();

        return $result->toArray()['components']['schemas'] ?? [];
    }
}
