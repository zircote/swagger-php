<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Analysers\TokenAnalyser;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property as AnnotationsProperty;
use OpenApi\Attributes\Property as AttributesProperty;
use OpenApi\Generator;
use OpenApi\Processors\ExpandEnums;
use OpenApi\Tests\Fixtures\PHP\StatusEnum;
use OpenApi\Tests\Fixtures\PHP\StatusEnumBacked;
use OpenApi\Tests\Fixtures\PHP\StatusEnumIntegerBacked;
use OpenApi\Tests\Fixtures\PHP\StatusEnumStringBacked;
use OpenApi\Tests\OpenApiTestCase;

class ExpandEnumsTest extends OpenApiTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (PHP_VERSION_ID < 80100 || $this->getAnalyzer() instanceof TokenAnalyser) {
            $this->markTestSkipped();
        }
    }

    public function testExpandUnitEnum(): void
    {
        $analysis = $this->analysisFromFixtures(['PHP/StatusEnum.php']);
        $analysis->process([new ExpandEnums()]);
        $schema = $analysis->getSchemaForSource(StatusEnum::class);

        self::assertEquals(['DRAFT', 'PUBLISHED', 'ARCHIVED'], $schema->enum);
    }

    public function testExpandBackedEnum(): void
    {
        $analysis = $this->analysisFromFixtures(['PHP/StatusEnumBacked.php']);
        $analysis->process([new ExpandEnums()]);
        $schema = $analysis->getSchemaForSource(StatusEnumBacked::class);

        self::assertEquals(['DRAFT', 'PUBLISHED', 'ARCHIVED'], $schema->enum);
    }

    public function testExpandBackedIntegerEnum(): void
    {
        $analysis = $this->analysisFromFixtures(['PHP/StatusEnumIntegerBacked.php']);
        $analysis->process([new ExpandEnums()]);
        $schema = $analysis->getSchemaForSource(StatusEnumIntegerBacked::class);

        self::assertEquals([1, 2, 3], $schema->enum);
    }

    public function testExpandBackedStringEnum(): void
    {
        $analysis = $this->analysisFromFixtures(['PHP/StatusEnumStringBacked.php']);
        $analysis->process([new ExpandEnums()]);
        $schema = $analysis->getSchemaForSource(StatusEnumStringBacked::class);

        self::assertEquals(['draft', 'published', 'archived'], $schema->enum);
    }

    public function expandEnumClassStringFixtures(): iterable
    {
        if (!class_exists('\\ReflectionEnum')) {
            // otherwise PHPUnit will run this for all PHP versions
            return [];
        }

        $mapNames = function (array $enums): array {
            return array_map(function ($c) {
                return $c->name;
            }, $enums);
        };

        $mapValues = function (array $enums): array {
            return array_map(function ($c) {
                return $c->value;
            }, $enums);
        };

        return [
            'statusEnum' => [
                ['PHP/ReferencesEnum.php'],
                'statusEnum',
                $mapNames(StatusEnum::cases()),
            ],
            'statusEnumBacked' => [
                ['PHP/ReferencesEnum.php'],
                'statusEnumBacked',
                $mapValues(StatusEnumBacked::cases()),
            ],
            'statusEnumIntegerBacked' => [
                ['PHP/ReferencesEnum.php'],
                'statusEnumIntegerBacked',
                $mapValues(StatusEnumIntegerBacked::cases()),
            ],
            'statusEnumStringBacked' => [['PHP/ReferencesEnum.php'], 'statusEnumStringBacked', $mapValues(StatusEnumStringBacked::cases())],
            'statusEnums' => [
                ['PHP/ReferencesEnum.php'],
                'statusEnums',
                Generator::UNDEFINED,
            ],
            'itemsStatusEnumStringBacked' => [
                ['PHP/ReferencesEnum.php'],
                'itemsStatusEnumStringBacked',
                $mapValues(StatusEnumStringBacked::cases()),
            ],
        ];
    }

    /**
     * @requires     PHP >= 8.1
     *
     * @dataProvider expandEnumClassStringFixtures
     */
    public function testExpandEnumClassString(array $files, string $title, mixed $expected): void
    {
        $analysis = $this->analysisFromFixtures($files);
        $analysis->process([new ExpandEnums()]);
        $schemas = $analysis->getAnnotationsOfType([AnnotationsProperty::class, AttributesProperty::class, Items::class], true);

        foreach ($schemas as $schema) {
            if ($schema instanceof AnnotationsProperty || $schema instanceof Items) {
                if ($schema->title == $title) {
                    self::assertEquals($expected, $schema->enum);
                }
            }
        }
    }
}
