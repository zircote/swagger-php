<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Builder;

use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Fixtures\Augmenter\RefTarget;
use PHPUnit\Framework\TestCase;

/**
 * `Builder::withSpecification()` is the seam for metadata with no reflector to scan, so these
 * cases contribute attributes built by hand and check they are treated as scanned ones are.
 */
final class SpecificationContributionTest extends TestCase
{
    public function testContributedAttributesReachTheDocument(): void
    {
        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->withSpecification(function (Specification $specification): void {
                $specification->add(
                    new OA\Info(title: 'Registered at runtime', version: '1.0.0'),
                    new OA\Operation\Get(path: '/users', responses: [
                        new OA\Response(response: 200, description: 'All users'),
                    ]),
                );
            })
            ->build();

        $document = $result->toArray();

        $this->assertSame('Registered at runtime', $document['info']['title']);
        $this->assertArrayHasKey('/users', $document['paths']);
    }

    public function testAContributedRefResolvesLikeAScannedOne(): void
    {
        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource(new \ReflectionClass(RefTarget::class))
            ->withSpecification(function (Specification $specification): void {
                $specification->add(
                    new OA\Info(title: 'Refs', version: '1.0.0'),
                    new OA\Operation\Get(path: '/targets', responses: [
                        new OA\Response(response: 200, description: 'A target', content: [
                            new OA\MediaType\Json(schema: new OA\Schema(ref: RefTarget::class)),
                        ]),
                    ]),
                );
            })
            ->build();

        $document = $result->toArray();

        $this->assertSame(
            '#/components/schemas/RefTarget',
            $document['paths']['/targets']['get']['responses'][200]['content']['application/json']['schema']['$ref'],
            'a class-string ref in a contribution resolves, which only happens before the resolver runs',
        );
    }

    public function testTheAugmentersSeeContributions(): void
    {
        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->withSpecification(function (Specification $specification): void {
                $specification->add(
                    new OA\Info(title: 'Augmented', version: '1.0.0'),
                    new OA\Operation\Get(path: '/things', tags: ['Things'], responses: [
                        new OA\Response(response: 200, description: 'Things'),
                    ]),
                );
            })
            ->build();

        $document = $result->toArray();

        $this->assertArrayHasKey(
            'operationId',
            $document['paths']['/things']['get'],
            'an operation with no reflector still gets an id, derived from method and path',
        );
        $this->assertSame('Things', $document['tags'][0]['name'], 'a tag used by a contribution is declared globally');
    }

    public function testHooksRunInRegistrationOrderAndAccumulate(): void
    {
        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->withSpecification(function (Specification $s): void {
                $s->add(new OA\Info(title: 'First', version: '1.0.0'));
            })
            ->withSpecification(function (Specification $s): void {
                $s->add(new OA\Operation\Get(path: '/second', responses: [new OA\Response(response: 200, description: 'ok')]));
            })
            ->build();

        $document = $result->toArray();

        $this->assertSame('First', $document['info']['title'], 'the first hook is not replaced by the second');
        $this->assertArrayHasKey('/second', $document['paths']);
    }

    public function testContributionsJoinScannedSourcesInOneDocument(): void
    {
        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource(new \ReflectionClass(RefTarget::class))
            ->withSpecification(function (Specification $s): void {
                $s->add(
                    new OA\Info(title: 'Both', version: '1.0.0'),
                    new OA\Operation\Get(path: '/mixed', responses: [
                        new OA\Response(response: 200, description: 'ok', content: [
                            new OA\MediaType\Json(schema: new OA\Schema(ref: RefTarget::class)),
                        ]),
                    ]),
                );
            })
            ->build();

        $document = $result->toArray();

        $this->assertArrayHasKey('RefTarget', $document['components']['schemas'], 'the scanned half survives');
        $this->assertArrayHasKey('/mixed', $document['paths'], 'the contributed half survives');
    }
}
