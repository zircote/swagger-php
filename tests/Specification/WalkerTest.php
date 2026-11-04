<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Specification;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use PHPUnit\Framework\TestCase;

final class WalkerTest extends TestCase
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

    /**
     * A list of classes is one walk over all of them. An attribute that is an instance of more
     * than one is still visited once.
     */
    public function testVisitTakesAListOfClasses(): void
    {
        $header = new OA\Header(header: 'X-Rate-Limit');
        $response = new OA\Response(response: 200, description: 'OK', headers: [$header]);

        $specification = new Specification();
        $specification->operations[] = new OA\Operation(path: '/products', method: 'get', responses: [$response]);

        $visited = [];
        $specification->getWalker()->visit(
            [OA\Response::class, OA\Header::class, AttributeInterface::class],
            function (AttributeInterface $attribute) use (&$visited): void {
                $visited[] = $attribute;
            }
        );

        $ids = array_map(spl_object_id(...), $visited);
        $this->assertSame(array_unique($ids), $ids, 'visited twice');
        $this->assertContains($header, $visited);
        $this->assertContains($response, $visited);
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

    /**
     * A property of a nested schema is visited once, with the schema it belongs to, and a
     * parameter or request body without one.
     */
    public function testEachTypedAttributeIsVisitedOnceWithItsSchema(): void
    {
        $address = new OA\Schema(properties: [new OA\Property(property: 'street')]);
        $customer = new OA\Schema(schema: 'Customer', properties: [new OA\Property(property: 'address', schema: $address)]);
        $parameter = new OA\Parameter(name: 'id', in: 'path');
        $requestBody = new OA\RequestBody(content: [new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: 'Customer'))]);

        $specification = new Specification();
        $specification->schemas[] = $customer;
        $specification->operations[] = new OA\Operation(path: '/customers/{id}', method: 'put', parameters: [$parameter], requestBody: $requestBody);

        $visited = new \SplObjectStorage();
        $specification->getWalker()->eachTypedAttribute(function (OA\Property|OA\Parameter|OA\RequestBody $attribute, ?OA\Schema $schema) use ($visited): void {
            $this->assertFalse($visited->offsetExists($attribute), 'visited twice');
            $visited[$attribute] = $schema;
        });

        $this->assertCount(4, $visited);
        $this->assertSame($address, $visited[$address->properties[0]]);
        $this->assertSame($customer, $visited[$customer->properties[0]]);
        $this->assertNull($visited[$parameter]);
        $this->assertNull($visited[$requestBody]);
    }
}
