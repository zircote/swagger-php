<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Assembler;
use OpenApi\Augmenter;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AugmenterTest extends TestCase
{
    protected function assemble(string ...$classes): Specification
    {
        $assembler = new Assembler();
        foreach ($classes as $class) {
            $assembler->collect(new \ReflectionClass($class));
        }

        return $assembler->getSpecification();
    }

    // --- Ref ---

    public function testRefResolvesFqcnToComponentPath(): void
    {
        $spec = $this->assemble(
            Fixtures\Augmenter\RefTarget::class,
            Fixtures\Augmenter\RefController::class,
        );

        (new Augmenter\Ref())($spec);

        $schema = $spec->operations[0]->responses[0]->content[0]->schema;
        $this->assertSame('#/components/schemas/RefTarget', $schema->ref);
    }

    public function testRefLeavesAlreadyResolvedUntouched(): void
    {
        $spec = new Specification();
        $schema = new OA\Schema(schema: 'Foo');
        $schema->setReflector(new \ReflectionClass(Fixtures\Augmenter\RefTarget::class));
        $spec->schemas[] = $schema;

        $operation = new OA\Operation(path: '/test', method: 'get');
        $response = new OA\Response(response: 200, description: 'OK', content: [
            new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: '#/components/schemas/Foo')),
        ]);
        $operation->responses = [$response];
        $spec->operations[] = $operation;

        (new Augmenter\Ref())($spec);

        $this->assertSame('#/components/schemas/Foo', $response->content[0]->schema->ref);
    }

    public function testRefResolvesDiscriminatorMapping(): void
    {
        $spec = $this->assemble(
            Fixtures\Augmenter\RefTarget::class,
            Fixtures\Augmenter\DiscriminatorSchema::class,
        );

        (new Augmenter\Ref())($spec);

        $discriminatorSchema = null;
        foreach ($spec->schemas as $schema) {
            if ($schema->schema === 'DiscriminatorSchema') {
                $discriminatorSchema = $schema;
                break;
            }
        }

        $this->assertInstanceOf(OA\Schema::class, $discriminatorSchema);
        $this->assertSame(
            ['target' => '#/components/schemas/RefTarget'],
            $discriminatorSchema->discriminator->mapping,
        );
    }

    // --- Docblock ---

    public function testDocblockAugmentsOperationSummaryAndDescription(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\DocblockController::class);

        (new Augmenter\Docblock())($spec);

        $this->assertSame('Get a thing.', $spec->operations[0]->summary);
        $this->assertSame('Returns the thing by ID.', $spec->operations[0]->description);
    }

    public function testDocblockAugmentsDeprecated(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\DocblockController::class);

        (new Augmenter\Docblock())($spec);

        $this->assertTrue($spec->operations[0]->deprecated);
    }

    public function testDocblockAugmentsSchemaDescription(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\DocblockSchema::class);

        (new Augmenter\Docblock())($spec);

        $this->assertSame('A documented schema.', $spec->schemas[0]->description);
        $this->assertTrue($spec->schemas[0]->deprecated);
    }

    public function testDocblockAugmentsParameterDescription(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\DocblockController::class);

        (new Augmenter\Docblock())($spec);

        $this->assertSame('the thing identifier', $spec->operations[0]->parameters[0]->description);
    }

    // --- OperationId ---

    #[DataProvider('operationIdProvider')]
    public function testOperationIdGeneration(bool $hash): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\DocblockController::class);

        $augmenter = new Augmenter\OperationId(hash: $hash);
        $augmenter($spec);

        $operationId = $spec->operations[0]->operationId;
        $this->assertNotNull($operationId);

        if ($hash) {
            $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $operationId);
        } else {
            $this->assertStringContainsString('getThing', $operationId);
        }
    }

    public static function operationIdProvider(): \Generator
    {
        yield 'hashed' => [true];
        yield 'clear text' => [false];
    }

    public function testOperationIdSkipsExplicit(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\DocblockController::class);
        $spec->operations[0]->operationId = 'custom';

        (new Augmenter\OperationId())($spec);

        $this->assertSame('custom', $spec->operations[0]->operationId);
    }

    // --- Tag ---

    public function testTagCreatesFromOperationUsage(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\TagController::class);

        (new Augmenter\Tag())($spec);

        $tagNames = array_map(fn (OA\Tag $t): ?string => $t->name, $spec->tags);
        $this->assertContains('alpha', $tagNames);
        $this->assertContains('beta', $tagNames);
    }

    public function testTagRemovesUnused(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\TagController::class);
        $spec->tags[] = new OA\Tag(name: 'unused');

        (new Augmenter\Tag())($spec);

        $tagNames = array_map(fn (OA\Tag $t): ?string => $t->name, $spec->tags);
        $this->assertNotContains('unused', $tagNames);
    }

    public function testTagWhitelistKeepsUnused(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\TagController::class);
        $spec->tags[] = new OA\Tag(name: 'unused');

        $augmenter = new Augmenter\Tag(whitelist: ['*']);
        $augmenter($spec);

        $tagNames = array_map(fn (OA\Tag $t): ?string => $t->name, $spec->tags);
        $this->assertContains('unused', $tagNames);
    }

    // --- Type ---

    public function testTypeInfersPropertyTypes(): void
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

    public function testTypeInfersParameterSchema(): void
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

    public function testTypeInfersParameterName(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\TypeController::class);

        (new Augmenter\Type())($spec);

        $this->assertSame('filter', $spec->operations[0]->parameters[1]->name);
    }

    public function testTypeSkipsExplicitSchema(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\TypeController::class);
        $spec->operations[0]->parameters[0]->schema = new OA\Schema(type: 'string');

        (new Augmenter\Type())($spec);

        $this->assertSame('string', $spec->operations[0]->parameters[0]->schema->type);
    }

    public function testTypeResolvesRefFromObjectType(): void
    {
        $spec = $this->assemble(
            Fixtures\Augmenter\RefTarget::class,
            Fixtures\Augmenter\RefController::class,
        );

        (new Augmenter\Type())($spec);

        // Type augmenter doesn't resolve refs to #/components/... — that's Ref's job
        // But it should leave the FQCN ref from the attribute as-is
        $schema = $spec->operations[0]->responses[0]->content[0]->schema;
        $this->assertSame(Fixtures\Augmenter\RefTarget::class, $schema->ref);
    }
}
