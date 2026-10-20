<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\HybridBridge;
use OpenApi\Spec as OA;
use OpenApi\Tests\Concerns\AssemblesSpecification;
use OpenApi\Tests\Fixtures;
use OpenApi\Undefined;
use PHPUnit\Framework\TestCase;

final class PropertiesTest extends TestCase
{
    use AssemblesSpecification;

    public function testInfersRequiredFromTypeAndDefault(): void
    {
        $schema = $this->augment(Fixtures\Augmenter\PropertyDeclarations::class);

        $this->assertSame(['id', 'born'], $schema->required);
    }

    public function testInfersDefaults(): void
    {
        $schemas = $this->propertySchemas($this->augment(Fixtures\Augmenter\PropertyDeclarations::class));

        $this->assertSame('new', $schemas['status']->default);
        $this->assertSame(4, $schemas['legs']->default);
        $this->assertSame(['pet'], $schemas['tags']->default);
        $this->assertSame('new', $schemas['state']->default, 'a backed enum case gives its value');
        $this->assertSame('Small', $schemas['size']->default, 'a unit enum case gives its name');
        $this->assertSame('x', $schemas['untyped']->default);
        $this->assertFalse($schemas['vaccinated']->default, 'a promoted property counts by its parameter');
        $this->assertSame('', $schemas['owner']->default);
    }

    public function testSkipsDefaultsTheDocumentCannotHold(): void
    {
        $schemas = $this->propertySchemas($this->augment(Fixtures\Augmenter\PropertyDeclarations::class));

        $this->assertTrue(Undefined::isDefault($schemas['note']->default), 'null is not taken');
        $this->assertTrue(Undefined::isDefault($schemas['adopted']->default), 'an object');
        $this->assertTrue(Undefined::isDefault($schemas['code']->default), 'a value the explicit type does not admit');
    }

    public function testKeepsWhatTheAttributeSets(): void
    {
        $schemas = $this->propertySchemas($this->augment(Fixtures\Augmenter\PropertyDeclarations::class));
        $this->assertSame('kept', $schemas['explicit']->default);
        $this->assertNull($schemas['suppressed']->default);

        $this->assertSame(['name'], $this->augment(Fixtures\Augmenter\ExplicitRequired::class)->required);
    }

    public function testLeavesBridgedSchemasAlone(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\ExplicitRequired::class);
        $schema = $spec->schemas[0];
        $schema->required = null;
        $schema->setMeta(HybridBridge::class, true);

        (new Augmenter\Types())($spec);
        (new Augmenter\Properties())($spec);

        $this->assertNull($schema->required);
    }

    /**
     * @param class-string $class
     */
    protected function augment(string $class): OA\Schema
    {
        $spec = $this->assemble($class);

        (new Augmenter\Types())($spec);
        (new Augmenter\Properties())($spec);

        return $spec->schemas[0];
    }

    /**
     * @return array<string, OA\Schema>
     */
    protected function propertySchemas(OA\Schema $schema): array
    {
        $schemas = [];
        foreach ($schema->properties ?? [] as $property) {
            if ($property instanceof OA\Property && $property->schema instanceof OA\Schema) {
                $schemas[$property->property] = $property->schema;
            }
        }

        return $schemas;
    }
}
