<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Compiler;

use OpenApi\Compiler\OpenApi31Compiler;
use OpenApi\Compiler\OpenApi31Parser;
use OpenApi\Loggers\CollectingLogger;
use OpenApi\Spec as OA;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * A document read and compiled again comes back as it was.
 *
 * As it was in content: the compiler writes keys in its own order, so the comparison ignores
 * the order of a map's keys, and checks the order of a list's members.
 */
final class OpenApi31ParserTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function documents(): iterable
    {
        $root = dirname(__DIR__, 2);
        $files = [
            ...glob($root . '/docs/examples/specs/*/*-3.1.0.yaml') ?: [],
            ...glob($root . '/tests/Fixtures/Scratch/*3.1.0.yaml') ?: [],
        ];

        foreach ($files as $file) {
            yield substr($file, strlen($root) + 1) => [$file];
        }
    }

    #[DataProvider('documents')]
    public function testADocumentCompilesBackUnchanged(string $file): void
    {
        $logger = new CollectingLogger();

        $specification = (new OpenApi31Parser($logger))->parse(Yaml::parseFile($file));
        $compiled = (new OpenApi31Compiler())->compile($specification);

        $this->assertSame([], array_column($logger->entries(), 'message'));
        $this->assertEquals($this->normalised(Yaml::parseFile($file)), $this->normalised($compiled));
    }

    public function testAKeywordWithNowhereToGoIsWarnedAboutWithItsLocation(): void
    {
        $logger = new CollectingLogger();

        (new OpenApi31Parser($logger))->parse([
            'openapi' => '3.1.0',
            'info' => ['title' => 'T', 'version' => '1', 'colour' => 'blue'],
            'paths' => ['/a/{id}' => ['get' => ['responses' => ['200' => ['description' => 'ok', 'shape' => 1]]]]],
        ]);

        $this->assertSame([
            '"colour" is not supported, left out at #/info',
            '"shape" is not supported, left out at #/paths/~1a~1{id}/get/responses/200',
        ], array_column($logger->entries(), 'message'));
    }

    public function testExtensionsGoToX(): void
    {
        $specification = (new OpenApi31Parser())->parse([
            'openapi' => '3.1.0',
            'info' => ['title' => 'T', 'version' => '1', 'x-logo' => ['url' => 'logo.png']],
        ]);

        $this->assertSame(['logo' => ['url' => 'logo.png']], $specification->info?->x);
    }

    public function testAComponentIsFiledUnderItsName(): void
    {
        $specification = (new OpenApi31Parser())->parse([
            'openapi' => '3.1.0',
            'components' => ['schemas' => ['Pet' => ['type' => 'object', 'properties' => ['name' => ['type' => 'string']]]]],
        ]);

        $this->assertSame(['Pet'], array_map(static fn (OA\Schema $schema): ?string => $schema->component, $specification->schemas));
        $this->assertSame('name', $specification->schemas[0]->properties[0]->property ?? null);
    }

    public function testTheSchemaTrueIsTheEmptySchemaAndFalseIsLeftOut(): void
    {
        $logger = new CollectingLogger();

        $compiled = $this->roundTrip([
            'openapi' => '3.1.0',
            'components' => ['schemas' => ['A' => [
                'type' => 'object',
                'properties' => ['any' => true, 'none' => false],
                'items' => false,
                'not' => true,
                'additionalProperties' => false,
            ]]],
        ], $logger);

        $this->assertSame([
            'The schema false is not supported, left out at #/components/schemas/A/properties/none',
            'The schema false is not supported, left out at #/components/schemas/A/items',
        ], array_column($logger->entries(), 'message'));
        $this->assertEquals([
            'type' => 'object',
            'properties' => ['any' => []],
            'not' => [],
            'additionalProperties' => false,
        ], $this->normalised($compiled['components']['schemas']['A']));
    }

    public function testANumberWhereAStringBelongsIsReadAsThatString(): void
    {
        $logger = new CollectingLogger();

        $specification = (new OpenApi31Parser($logger))->parse(['openapi' => '3.1.0', 'info' => ['title' => 2024, 'version' => 1.0]]);

        $this->assertSame('2024', $specification->info?->title);
        $this->assertSame('1.0', $specification->info->version);
        $this->assertSame(['Expected a string at #/info/version, read the number 1.0 as "1.0"'], array_column($logger->entries(), 'message'));
    }

    public function testAValueOfTheWrongTypeIsLeftOut(): void
    {
        $logger = new CollectingLogger();

        $compiled = $this->roundTrip([
            'openapi' => '3.1.0',
            'paths' => ['/a' => [
                'get' => ['tags' => 'a', 'responses' => ['200' => ['description' => 'ok']]],
                'post' => 'nothing',
            ]],
            'components' => ['schemas' => ['A' => ['type' => 'string', 'minLength' => '3']]],
        ], $logger);

        $this->assertSame([
            'Expected ?array at #/paths/~1a/get/tags, found string, left out',
            'Expected an object at #/paths/~1a/post, left out',
            'Expected ?int at #/components/schemas/A/minLength, found string, left out',
        ], array_column($logger->entries(), 'message'));
        $this->assertSame(['get'], array_keys($compiled['paths']['/a']));
        $this->assertSame(['type' => 'string'], $compiled['components']['schemas']['A']);
    }

    public function testAnEmptyPathIsKept(): void
    {
        $logger = new CollectingLogger();

        $compiled = $this->roundTrip(['openapi' => '3.1.0', 'paths' => ['/a' => []], 'webhooks' => ['w' => []]], $logger);

        $this->assertEquals(['/a' => new \stdClass()], $compiled['paths']);
        $this->assertSame(['A webhook without operations is not supported, left out at #/webhooks/w'], array_column($logger->entries(), 'message'));
    }

    /**
     * @param array<string, mixed> $document
     *
     * @return array<string, mixed>
     */
    private function roundTrip(array $document, CollectingLogger $logger): array
    {
        return (new OpenApi31Compiler())->compile((new OpenApi31Parser($logger))->parse($document));
    }

    /**
     * Objects and arrays alike, as JSON reads them back, so `{}` and an empty map compare equal.
     *
     * A `type` list of one is that type: the compiler writes it bare, some fixtures as a list.
     */
    private function normalised(mixed $document): mixed
    {
        return $this->singleTypes(json_decode(json_encode($document, JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION), true, flags: JSON_THROW_ON_ERROR));
    }

    private function singleTypes(mixed $value): mixed
    {
        if (!is_array($value)) {
            return $value;
        }
        if (is_array($value['type'] ?? null) && count($value['type']) === 1) {
            $value['type'] = reset($value['type']);
        }

        return array_map($this->singleTypes(...), $value);
    }
}
