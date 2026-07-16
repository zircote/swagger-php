<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Assembler;
use OpenApi\Augmenter;
use OpenApi\Tests\Concerns\AssertsSchemaStructure;
use OpenApi\Tests\Fixtures\Augmenter\Hierarchy\Spec as Fixtures;
use PHPUnit\Framework\TestCase;

final class InheritanceTest extends TestCase
{
    use AssertsSchemaStructure;

    public function testAllSchemasMatchExpected(): void
    {
        $assembler = new Assembler();
        $assembler->collect(
            new \ReflectionClass(Fixtures\TraitWithSchema::class),
            new \ReflectionClass(Fixtures\ClassUsingTraitWithSchema::class),
            new \ReflectionClass(Fixtures\PlainTrait::class),
            new \ReflectionClass(Fixtures\ClassUsingPlainTrait::class),
            new \ReflectionClass(Fixtures\ParentWithSchema::class),
            new \ReflectionClass(Fixtures\ChildOfParentWithSchema::class),
            new \ReflectionClass(Fixtures\PlainParent::class),
            new \ReflectionClass(Fixtures\ChildOfPlainParent::class),
            new \ReflectionClass(Fixtures\StandaloneSchema::class),
        );

        $spec = $assembler->getSpecification();
        (new Augmenter\Inheritance())($spec);

        $this->assertSpecificationSchemasMatchFile(
            $spec,
            __DIR__ . '/../Fixtures/Augmenter/Hierarchy/expected.yaml',
        );
    }
}
