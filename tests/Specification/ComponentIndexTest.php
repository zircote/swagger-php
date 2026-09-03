<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Specification;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Concerns\AssemblesSpecification;
use OpenApi\Tests\Fixtures\ComponentIndex\OddlyNamed;
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
        $this->assertNotInstanceOf(AttributeInterface::class, $index->find('#/components/schemas/UnnamedTag'));
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
        $this->assertNotInstanceOf(AttributeInterface::class, $this->assemble(Product::class)->buildComponentIndex()->find($ref));
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
        $this->assertNotInstanceOf(OA\Schema::class, $index->findSchema('#/components/responses/shared'));
        $this->assertNotInstanceOf(OA\Response::class, $index->findResponse('#/components/schemas/shared'));
    }

    public function testBareNameResolvesWithinTheFindersOwnBucket(): void
    {
        $index = (new Specification())
            ->add(new OA\Response(response: 'notFound', description: 'gone'))
            ->buildComponentIndex();

        $this->assertInstanceOf(OA\Response::class, $index->findResponse('notFound'));
        $this->assertNotInstanceOf(OA\Schema::class, $index->findSchema('notFound'));
    }

    public function testParameterFallsBackToNameWhenUnkeyed(): void
    {
        $index = (new Specification())
            ->add(new OA\Parameter(name: 'page', in: 'query'))
            ->buildComponentIndex();

        $this->assertInstanceOf(OA\Parameter::class, $index->findParameter('#/components/parameters/page'));
    }

    /**
     * `/` and `~` are structural in a JSON Pointer, so a component name containing either has
     * to be escaped where it is embedded in a `$ref` — and left raw as the map key, which is
     * what the compiler emits under `components.schemas`.
     */
    public function testRefMapEscapesTheComponentName(): void
    {
        $map = $this->assemble(OddlyNamed::class)->buildComponentIndex()->buildRefMap();

        $this->assertSame('#/components/schemas/Odd~1Name~0With', $map[OddlyNamed::class]);
    }

    /**
     * The escaped form is what `buildRefMap()` produces and so what the pipeline resolves. The
     * raw form resolves as well, because everything after the bucket is taken as the name —
     * lenient rather than designed, but worth pinning so a stricter parse is a deliberate
     * change.
     */
    public function testFindResolvesAnEscapedComponentPath(): void
    {
        $index = $this->assemble(OddlyNamed::class)->buildComponentIndex();

        $escaped = $index->find('#/components/schemas/Odd~1Name~0With');

        $this->assertInstanceOf(OA\Schema::class, $escaped);
        $this->assertSame($escaped, $index->find('#/components/schemas/Odd/Name~With'));
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
