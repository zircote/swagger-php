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

final class TypeTest extends TestCase
{
    protected function assemble(string ...$classes): Specification
    {
        $assembler = new Assembler();
        foreach ($classes as $class) {
            $assembler->collect(new \ReflectionClass($class));
        }

        return $assembler->getSpecification();
    }

    public function testInfersPropertyTypes(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\TypeSchema::class);

        (new Augmenter\Type())($spec);

        $schema = $spec->schemas[0];
        $this->assertSame('TypeSchema', $schema->schema);

        $props = [];
        foreach ($schema->properties as $property) {
            $props[$property->property] = $property->schema;
        }

        $this->assertSame('integer', $props['id']->type);
        $this->assertSame('string', $props['name']->type);
        $this->assertSame('number', $props['score']->type);
        $this->assertTrue($props['score']->nullable);
        $this->assertSame('boolean', $props['active']->type);
        $this->assertSame('array', $props['tags']->type);
        $this->assertSame('string', $props['tags']->items->type);
    }

    public function testInfersParameterSchema(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\TypeController::class);

        (new Augmenter\Type())($spec);

        $params = $spec->operations[0]->parameters;
        $this->assertSame('integer', $params[0]->schema->type);
        $this->assertTrue($params[0]->required);

        $this->assertSame('string', $params[1]->schema->type);
        $this->assertTrue($params[1]->schema->nullable);
        $this->assertFalse($params[1]->required);
    }

    public function testInfersParameterName(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\TypeController::class);

        (new Augmenter\Type())($spec);

        $this->assertSame('filter', $spec->operations[0]->parameters[1]->name);
    }

    public function testSkipsExplicitSchema(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\TypeController::class);
        $spec->operations[0]->parameters[0]->schema = new OA\Schema(type: 'string');

        (new Augmenter\Type())($spec);

        $this->assertSame('string', $spec->operations[0]->parameters[0]->schema->type);
    }

    public function testResolvesRefFromObjectType(): void
    {
        $spec = $this->assemble(
            Fixtures\Augmenter\RefTarget::class,
            Fixtures\Augmenter\RefController::class,
        );

        (new Augmenter\Type())($spec);

        $schema = $spec->operations[0]->responses[0]->content[0]->schema;
        $this->assertSame(Fixtures\Augmenter\RefTarget::class, $schema->ref);
    }
}
