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

    /**
     * @requires PHP >= 8.1
     */
    public function testExpandEnumClassString(): void
    {
        $analysis = $this->analysisFromFixtures(['PHP/ReferencesEnum.php']);
        $analysis->process([new ExpandEnums()]);
        $schemas = $analysis->getAnnotationsOfType([AnnotationsProperty::class, AttributesProperty::class, Items::class], true);

        $expected = [
            'statusEnum' => array_map(fn ($c) => $c->name, StatusEnum::cases()),
            'statusEnumBacked' => array_map(fn ($c) => $c->value, StatusEnumBacked::cases()),
            'statusEnumIntegerBacked' => array_map(fn ($c) => $c->value, StatusEnumIntegerBacked::cases()),
            'statusEnumStringBacked' => array_map(fn ($c) => $c->value, StatusEnumStringBacked::cases()),
            'statusEnums' => Generator::UNDEFINED,
            'itemsStatusEnumStringBacked' => array_map(fn ($c) => $c->value, StatusEnumStringBacked::cases()),
        ];

        foreach ($schemas as $schema) {
            self::assertEquals($expected[$schema->title], $schema->enum);
        }
    }
}
