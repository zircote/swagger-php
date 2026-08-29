<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Specification;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Concerns\AssemblesSpecification;
use OpenApi\Tests\Fixtures\ComponentIndex\Product;
use OpenApi\Tests\Fixtures\ComponentIndex\UnnamedTag;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ComponentIndexTest extends TestCase
{
    use AssemblesSpecification;

    public function testFindsByComponentPathAndByFqcn(): void
    {
        $index = $this->assemble(Product::class)->buildComponentIndex();

        $byPath = $index->find('#/components/schemas/product');
        $byFqcn = $index->find(Product::class);

        $this->assertInstanceOf(OA\Schema::class, $byPath);
        $this->assertSame($byPath, $byFqcn, 'both lookups should resolve to the same instance');
    }

    public function testFindsUnnamedComponentByFqcnOnly(): void
    {
        $index = $this->assemble(UnnamedTag::class)->buildComponentIndex();

        $this->assertInstanceOf(OA\Schema::class, $index->find(UnnamedTag::class));
        $this->assertNull($index->find('#/components/schemas/UnnamedTag'));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function unresolvableRefs(): iterable
    {
        yield 'unknown name' => ['#/components/schemas/nope'];
        yield 'unknown bucket' => ['#/components/nope/product'];
        yield 'bucket without a name' => ['#/components/schemas'];
        yield 'unknown fqcn' => ['App\\Nope'];
        yield 'empty' => [''];
    }

    #[DataProvider('unresolvableRefs')]
    public function testReturnsNullForUnresolvableRefs(string $ref): void
    {
        $this->assertNull($this->assemble(Product::class)->buildComponentIndex()->find($ref));
    }

    public function testTypedFindersFilterByType(): void
    {
        $specification = (new Specification())->add(
            new OA\Schema(schema: 'shared'),
            new OA\Response(response: 'shared', description: 'ok'),
        );
        $index = $specification->buildComponentIndex();

        $this->assertInstanceOf(OA\Schema::class, $index->findSchema('#/components/schemas/shared'));
        $this->assertInstanceOf(OA\Response::class, $index->findResponse('#/components/responses/shared'));

        // the name exists, but in the other bucket
        $this->assertNull($index->findSchema('#/components/responses/shared'));
        $this->assertNull($index->findResponse('#/components/schemas/shared'));
    }

    public function testBareNameResolvesWithinTheFindersOwnBucket(): void
    {
        $index = (new Specification())
            ->add(new OA\Response(response: 'notFound', description: 'gone'))
            ->buildComponentIndex();

        $this->assertInstanceOf(OA\Response::class, $index->findResponse('notFound'));
        $this->assertNull($index->findSchema('notFound'));
    }

    public function testParameterFallsBackToNameWhenUnkeyed(): void
    {
        $index = (new Specification())
            ->add(new OA\Parameter(name: 'page', in: 'query'))
            ->buildComponentIndex();

        $this->assertInstanceOf(OA\Parameter::class, $index->findParameter('#/components/parameters/page'));
    }

    public function testBuildRefMapMapsFqcnToComponentPath(): void
    {
        $map = $this->assemble(Product::class)->buildComponentIndex()->buildRefMap();

        $this->assertSame(['#/components/schemas/product'], array_values(array_intersect_key(
            $map,
            [Product::class => null]
        )));
    }

    public function testBuildRefMapSkipsComponentsWithoutAName(): void
    {
        $map = $this->assemble(UnnamedTag::class)->buildComponentIndex()->buildRefMap();

        $this->assertArrayNotHasKey(UnnamedTag::class, $map);
    }
}
