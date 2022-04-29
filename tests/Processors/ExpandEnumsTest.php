<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Processors\ExpandEnums;
use OpenApi\Tests\Fixtures\PHP\StatusEnum;
use OpenApi\Tests\Fixtures\PHP\StatusEnumBacked;
use OpenApi\Tests\OpenApiTestCase;

class ExpandEnumsTest extends OpenApiTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        if (! class_exists('\\ReflectionEnum')) {
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
}