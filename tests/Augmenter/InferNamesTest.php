<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Assembler;
use OpenApi\Augmenter;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Fixtures;
use PHPUnit\Framework\TestCase;

final class InferNamesTest extends TestCase
{
    protected function assemble(string ...$classes): Specification
    {
        $assembler = new Assembler();
        foreach ($classes as $class) {
            $assembler->collect(new \ReflectionClass($class));
        }

        return $assembler->getSpecification();
    }

    public function testInfersSchemaNameFromClass(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\TypeSchema::class);

        (new Augmenter\InferNames())($spec);

        $this->assertSame('TypeSchema', $spec->schemas[0]->schema);
    }

    public function testInfersSchemaNameFromEnum(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\BasicEnum::class);

        (new Augmenter\InferNames())($spec);

        $this->assertSame('BasicEnum', $spec->schemas[0]->schema);
    }

    public function testPreservesExplicitSchemaName(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\TypeSchema::class);
        $spec->schemas[0]->schema = 'CustomName';

        (new Augmenter\InferNames())($spec);

        $this->assertSame('CustomName', $spec->schemas[0]->schema);
    }

    public function testInfersParameterKeyFromName(): void
    {
        $spec = new Specification();
        $param = new OA\Parameter(name: 'page', in: 'query');
        $spec->parameters[] = $param;

        (new Augmenter\InferNames())($spec);

        $this->assertSame('page', $param->parameter);
    }

    public function testPreservesExplicitParameterKey(): void
    {
        $spec = new Specification();
        $param = new OA\Parameter(parameter: 'custom', name: 'page', in: 'query');
        $spec->parameters[] = $param;

        (new Augmenter\InferNames())($spec);

        $this->assertSame('custom', $param->parameter);
    }
}
