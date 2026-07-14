<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Concerns\AssemblesSpecification;
use OpenApi\Tests\Fixtures;
use PHPUnit\Framework\TestCase;

final class RefTest extends TestCase
{
    use AssemblesSpecification;

    public function testResolvesFqcnToComponentPath(): void
    {
        $spec = $this->assemble(
            Fixtures\Augmenter\RefTarget::class,
            Fixtures\Augmenter\RefController::class,
        );

        (new Augmenter\Ref())($spec);

        $schema = $spec->operations[0]->responses[0]->content[0]->schema;
        $this->assertSame('#/components/schemas/RefTarget', $schema->ref);
    }

    public function testLeavesAlreadyResolvedUntouched(): void
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

    public function testResolvesDiscriminatorMapping(): void
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
}
