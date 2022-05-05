<?php
declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Analysers\TokenAnalyser;
use OpenApi\Processors\ExpandEnums;
use OpenApi\Tests\Fixtures\PHP\StatusEnum;
use OpenApi\Tests\Fixtures\PHP\StatusEnumBacked;
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

    public function testExpandBackedStringEnum(): void
    {
        $analysis = $this->analysisFromFixtures(['PHP/StatusEnumStringBacked.php']);
        $analysis->process([new ExpandEnums()]);
        $schema = $analysis->getSchemaForSource(StatusEnumStringBacked::class);

        self::assertEquals(['draft', 'published', 'archived'], $schema->enum);
    }
}
