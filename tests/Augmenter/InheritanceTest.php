<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Assembler;
use OpenApi\Augmenter;
use OpenApi\Spec as OA;
use OpenApi\Tests\Concerns\AssemblesSpecification;
use OpenApi\Tests\Concerns\AssertsSchemaStructure;
use OpenApi\Tests\Fixtures as OperationalFixtures;
use OpenApi\Tests\Fixtures\Augmenter\Hierarchy\Spec as HierarchyFixtures;
use PHPUnit\Framework\TestCase;

final class InheritanceTest extends TestCase
{
    use AssemblesSpecification;
    use AssertsSchemaStructure;

    public function testAllSchemasMatchExpected(): void
    {
        $assembler = new Assembler();
        $assembler->collect(
            new \ReflectionClass(HierarchyFixtures\TraitWithSchema::class),
            new \ReflectionClass(HierarchyFixtures\ClassUsingTraitWithSchema::class),
            new \ReflectionClass(HierarchyFixtures\PlainTrait::class),
            new \ReflectionClass(HierarchyFixtures\ClassUsingPlainTrait::class),
            new \ReflectionClass(HierarchyFixtures\ParentWithSchema::class),
            new \ReflectionClass(HierarchyFixtures\ChildOfParentWithSchema::class),
            new \ReflectionClass(HierarchyFixtures\PlainParent::class),
            new \ReflectionClass(HierarchyFixtures\ChildOfPlainParent::class),
            new \ReflectionClass(HierarchyFixtures\StandaloneSchema::class),
            new \ReflectionClass(HierarchyFixtures\ClassUsingOrderedTraits::class),
        );

        $spec = $assembler->getSpecification();
        (new Augmenter\Inheritance())($spec);
        (new Augmenter\Names())($spec);
        (new Augmenter\Refs())($spec);

        $this->assertSpecificationSchemasMatchFile(
            $spec,
            __DIR__ . '/../Fixtures/Augmenter/Hierarchy/expected.yaml',
        );
    }

    /**
     * A resolver that builds a class's schema from another description has the hierarchy in it
     * already; with a discriminator map on the parent, adding the parent again makes a cycle.
     */
    public function testASchemaMarkedByItsBuilderKeepsItsShape(): void
    {
        $assembler = new Assembler();
        $assembler->collect(new \ReflectionClass(HierarchyFixtures\ParentWithSchema::class));
        $spec = $assembler->getSpecification();
        $built = static fn (\ReflectionClass $class): OA\Schema => (new OA\Schema(properties: [
            new OA\Property(property: 'parentProp', schema: new OA\Schema(type: 'string')),
        ]))->setReflector($class);
        $spec->add($marked = $built(new \ReflectionClass(HierarchyFixtures\ChildOfParentWithSchema::class))->setMeta(Augmenter\Inheritance\Schemas::class, false));
        $spec->add($mergedInto = $built(new \ReflectionClass(HierarchyFixtures\ChildOfPlainParent::class))->setMeta(Augmenter\Inheritance\Schemas::class, false));

        (new Augmenter\Inheritance())($spec);

        $this->assertNull($marked->allOf);
        $this->assertSame(['parentProp'], array_map(static fn (OA\Property $property): ?string => $property->property, $mergedInto->properties ?? []));
    }

    public function testAnUnmarkedSchemaBuiltElsewhereGetsItsParent(): void
    {
        $assembler = new Assembler();
        $assembler->collect(new \ReflectionClass(HierarchyFixtures\ParentWithSchema::class));
        $spec = $assembler->getSpecification();
        $spec->add($schema = (new OA\Schema())->setReflector(new \ReflectionClass(HierarchyFixtures\ChildOfParentWithSchema::class)));

        (new Augmenter\Inheritance())($spec);

        $this->assertCount(1, $schema->allOf ?? []);
    }

    public function testInheritedOperationClonedToChild(): void
    {
        $spec = $this->assemble(
            OperationalFixtures\Augmenter\AbstractDocumentController::class,
            OperationalFixtures\Augmenter\InvoiceDocumentController::class,
        );

        $this->assertCount(1, $spec->operations, 'Assembled from abstract parent');
        $this->assertSame(
            OperationalFixtures\Augmenter\AbstractDocumentController::class,
            $spec->operations[0]->getClassName(),
        );

        (new Augmenter\Inheritance())($spec);

        $this->assertCount(1, $spec->operations, 'Still one operation (original replaced by clone)');
        $this->assertSame(
            OperationalFixtures\Augmenter\InvoiceDocumentController::class,
            $spec->operations[0]->getClassName(),
            'Operation now associated with child class',
        );
    }

    /**
     * Augmenters change responses in place, so each child needs its own.
     */
    public function testEachChildGetsItsOwnCopyOfAnInheritedOperation(): void
    {
        $spec = $this->assemble(
            OperationalFixtures\Augmenter\AbstractDocumentController::class,
            OperationalFixtures\Augmenter\InvoiceDocumentController::class,
            OperationalFixtures\Augmenter\TaggedArchiveController::class,
        );

        (new Augmenter\Inheritance())($spec);

        $this->assertCount(2, $spec->operations);
        [$invoices, $archive] = $spec->operations;
        $this->assertEquals($invoices->responses, $archive->responses);
        $this->assertNotSame($invoices->responses[0] ?? null, $archive->responses[0] ?? null);
    }

    public function testPathItemPrefixAppliedToInheritedOperation(): void
    {
        $spec = $this->assemble(
            OperationalFixtures\Augmenter\AbstractDocumentController::class,
            OperationalFixtures\Augmenter\InvoiceDocumentController::class,
        );

        (new Augmenter\Inheritance())($spec);
        (new Augmenter\PathItems())($spec);

        $this->assertSame('/invoices', $spec->operations[0]->path);
    }

    public function testTagsClonedToInheritedOperation(): void
    {
        $spec = $this->assemble(
            OperationalFixtures\Augmenter\AbstractDocumentController::class,
            OperationalFixtures\Augmenter\InvoiceDocumentController::class,
        );

        (new Augmenter\Inheritance())($spec);
        (new Augmenter\PathItems())($spec);

        $this->assertSame(['Invoices'], $spec->operations[0]->tags);
    }

    public function testDiscoveryWhenOnlyChildAssembled(): void
    {
        $spec = $this->assemble(
            OperationalFixtures\Augmenter\InvoiceDocumentController::class,
        );

        $this->assertCount(0, $spec->operations, 'Assembler skips inherited methods');

        (new Augmenter\Inheritance())($spec);

        $this->assertCount(1, $spec->operations, 'Discovered from unvisited ancestor');
        $this->assertSame(
            OperationalFixtures\Augmenter\InvoiceDocumentController::class,
            $spec->operations[0]->getClassName(),
            'Associated with child class',
        );
    }

    public function testDiscoveryPrefixApplied(): void
    {
        $spec = $this->assemble(
            OperationalFixtures\Augmenter\InvoiceDocumentController::class,
        );

        (new Augmenter\Inheritance())($spec);
        (new Augmenter\PathItems())($spec);

        $this->assertSame('/invoices', $spec->operations[0]->path);
    }

    public function testDirectOperationsNotDuplicated(): void
    {
        $spec = $this->assemble(
            OperationalFixtures\Augmenter\PathItemBaseController::class,
            OperationalFixtures\Augmenter\PathItemUserController::class,
        );

        $countBefore = count($spec->operations);

        (new Augmenter\Inheritance())($spec);

        $this->assertCount($countBefore, $spec->operations, 'Already-collected operations not duplicated');
    }
}
