<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Utils;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use PHPUnit\Framework\TestCase;

final class SpecificationWalkerTest extends TestCase
{
    /**
     * A reusable response sits in the `responses` bucket and in the operation that uses it.
     * It is one attribute, and a visitor that counts occurrences has to see it once — a
     * duplicate reads as two operations sharing an id, or two schemas sharing a name.
     */
    public function testAttributeReachableFromTwoBucketsIsVisitedOnce(): void
    {
        $shared = new OA\Response(response: 'product', description: 'a reusable response');

        $specification = new Specification();
        $specification->responses[] = $shared;
        $specification->operations[] = new OA\Operation(path: '/products', method: 'get', responses: [$shared]);

        $visited = [];
        $specification->getWalker()->visit(OA\Response::class, function (OA\Response $response) use (&$visited): void {
            $visited[] = $response;
        });

        $this->assertSame([$shared], $visited);
    }

    public function testNestedAttributesAreVisited(): void
    {
        $specification = new Specification();
        $specification->schemas[] = new OA\Schema(schema: 'Product', properties: [
            new OA\Property(property: 'name', schema: new OA\Schema(type: 'string')),
        ]);

        $properties = [];
        $specification->getWalker()->visit(OA\Property::class, function (OA\Property $property) use (&$properties): void {
            $properties[] = $property->property;
        });

        $this->assertSame(['name'], $properties);
    }
}
