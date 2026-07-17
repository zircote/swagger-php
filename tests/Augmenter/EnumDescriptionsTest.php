<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Spec as OA;
use OpenApi\Tests\Concerns\AssemblesSpecification;
use OpenApi\Tests\Fixtures;
use PHPUnit\Framework\TestCase;

final class EnumDescriptionsTest extends TestCase
{
    use AssemblesSpecification;

    public function testBasicEnumUsesNames(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\EnumProperties::class);

        (new Augmenter\Types())($spec);
        (new Augmenter\EnumDescriptions(enabled: true))($spec);

        $this->assertCount(1, $spec->schemas);
        $schema = $spec->schemas[0];

        $this->assertCount(2, $schema->properties);
        $properties = array_combine(
            array_map(fn (OA\Property $property): ?string => $property->property, $schema->properties),
            $schema->properties,
        );

        $this->assertArrayHasKey('colour', $properties);
        $this->assertSame('BasicEnum (GREEN; BLUE; RED)', $properties['colour']->schema->description);

        $this->assertArrayHasKey('status', $properties);
        $this->assertSame('BackedStringEnum (active:ACTIVE; inactive:INACTIVE)', $properties['status']->schema->description);
        // also check we preserve the existing schema
        $this->assertSame('Some status', $properties['status']->schema->title);
    }
}
