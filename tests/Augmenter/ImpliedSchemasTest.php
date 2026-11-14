<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Specification\ComponentKey;
use OpenApi\Tests\Fixtures;
use OpenApi\Type\Generics\TypeArguments;
use PHPUnit\Framework\TestCase;
use Symfony\Component\TypeInfo\Type;

final class ImpliedSchemasTest extends TestCase
{
    /**
     * `Types` references the enum from the property, so its schema has to be there.
     */
    public function testAnEnumAPropertyReadsAsInItsComponentGetsASchema(): void
    {
        $specification = $this->specification(new TypeArguments(Type::enum(Fixtures\Augmenter\BackedStringEnum::class)));

        (new Augmenter\ImpliedSchemas())($specification);

        $this->assertCount(2, $specification->schemas);
        $this->assertSame(Fixtures\Augmenter\BackedStringEnum::class, (string) $specification->schemas[1]->getComponentKey());
    }

    public function testAPropertyReadWithoutTypeArgumentsImpliesNothing(): void
    {
        $specification = $this->specification(null);

        (new Augmenter\ImpliedSchemas())($specification);

        $this->assertCount(1, $specification->schemas);
    }

    private function specification(?TypeArguments $typeArguments): Specification
    {
        $own = (new OA\Property(property: 'own'))->setReflector(new \ReflectionProperty(Fixtures\Type\StampedBox::class, 'own'));
        $schema = (new OA\Schema(properties: [$own]))
            ->setReflector(new \ReflectionClass(Fixtures\Type\StampedBox::class))
            ->setComponentKey(new ComponentKey(Fixtures\Type\StampedBox::class, typeArguments: $typeArguments));
        $specification = new Specification();
        $specification->schemas[] = $schema;

        return $specification;
    }
}
