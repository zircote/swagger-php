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

final class RefsTest extends TestCase
{
    use AssemblesSpecification;

    public function testResolvesFqcnToComponentPath(): void
    {
        $spec = $this->assemble(
            Fixtures\Augmenter\RefTarget::class,
            Fixtures\Augmenter\RefController::class,
        );

        (new Augmenter\Refs())($spec);

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

        (new Augmenter\Refs())($spec);

        $this->assertSame('#/components/schemas/Foo', $response->content[0]->schema->ref);
    }

    public function testResolvesRefInstanceOnSchema(): void
    {
        $spec = $this->assemble(
            Fixtures\Augmenter\RefTarget::class,
            Fixtures\Augmenter\RefInstanceController::class,
        );

        (new Augmenter\Refs())($spec);

        $operation = null;
        foreach ($spec->operations as $op) {
            if ($op->path === '/ref-instance-schema') {
                $operation = $op;
                break;
            }
        }

        $this->assertInstanceOf(OA\Operation::class, $operation);
        $schema = $operation->responses[0]->content[0]->schema;
        $this->assertSame('#/components/schemas/RefTarget', $schema->ref);
    }

    public function testResolvesRefInstanceOnResponse(): void
    {
        $spec = $this->assemble(
            Fixtures\Augmenter\RefTarget::class,
            Fixtures\Augmenter\RefInstanceController::class,
        );

        $sharedResponse = new OA\Response(response: 'SharedResponse', description: 'Shared');
        $sharedResponse->setReflector(new \ReflectionClass(Fixtures\Augmenter\RefTarget::class));
        $spec->responses[] = $sharedResponse;

        (new Augmenter\Refs())($spec);

        $operation = null;
        foreach ($spec->operations as $op) {
            if ($op->path === '/ref-instance-response') {
                $operation = $op;
                break;
            }
        }

        $this->assertInstanceOf(OA\Operation::class, $operation);
        $this->assertSame('#/components/responses/SharedResponse', $operation->responses[0]->ref);
    }

    public function testResolvesDiscriminatorMapping(): void
    {
        $spec = $this->assemble(
            Fixtures\Augmenter\RefTarget::class,
            Fixtures\Augmenter\DiscriminatorSchema::class,
        );

        (new Augmenter\Refs())($spec);

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
