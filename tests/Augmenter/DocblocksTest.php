<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Tests\Concerns\AssemblesSpecification;
use OpenApi\Tests\Fixtures;
use PHPUnit\Framework\TestCase;

final class DocblocksTest extends TestCase
{
    use AssemblesSpecification;

    public function testAugmentsOperationSummaryAndDescription(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\DocblockController::class);

        (new Augmenter\Docblocks())($spec);

        $this->assertSame('Get a thing.', $spec->operations[0]->summary);
        $this->assertSame('Returns the thing by ID.', $spec->operations[0]->description);
    }

    public function testAugmentsDeprecated(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\DocblockController::class);

        (new Augmenter\Docblocks())($spec);

        $this->assertTrue($spec->operations[0]->deprecated);
    }

    public function testAugmentsSchemaDescription(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\DocblockSchema::class);

        (new Augmenter\Docblocks())($spec);

        $this->assertSame('A documented schema.', $spec->schemas[0]->description);
        $this->assertTrue($spec->schemas[0]->deprecated);
    }

    public function testAugmentsParameterDescription(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\DocblockController::class);

        (new Augmenter\Docblocks())($spec);

        $this->assertSame('the thing identifier', $spec->operations[0]->parameters[0]->description);
    }
}
