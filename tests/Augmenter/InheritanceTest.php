<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Assembler;
use OpenApi\Augmenter;
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
