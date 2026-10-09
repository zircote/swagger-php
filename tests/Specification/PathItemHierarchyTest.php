<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Specification;

use OpenApi\Spec as OA;
use OpenApi\Tests\Concerns\AssemblesSpecification;
use OpenApi\Tests\Fixtures\Augmenter\PathItemBaseController;
use OpenApi\Tests\Fixtures\Augmenter\PathItemContract;
use OpenApi\Tests\Fixtures\Augmenter\PathItemContractController;
use OpenApi\Tests\Fixtures\Augmenter\PathItemGrandparentController;
use OpenApi\Tests\Fixtures\Augmenter\PathItemInheritedController;
use OpenApi\Tests\Fixtures\Augmenter\PathItemLeafController;
use OpenApi\Tests\Fixtures\Augmenter\PathItemMiddleController;
use OpenApi\Tests\Fixtures\Augmenter\PathItemPlainController;
use OpenApi\Tests\Fixtures\Augmenter\PathItemUserController;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PathItemHierarchyTest extends TestCase
{
    use AssemblesSpecification;

    public static function chainProvider(): \Generator
    {
        $threeLevel = [PathItemGrandparentController::class, PathItemMiddleController::class, PathItemLeafController::class];
        $inherited = [PathItemBaseController::class, PathItemUserController::class, PathItemInheritedController::class];

        yield 'outermost first, own item last' => [$threeLevel, PathItemLeafController::class, ['/api', '/v2', '/orders']];

        yield 'a descendant contributes nothing to its ancestor' => [$threeLevel, PathItemMiddleController::class, ['/api', '/v2']];

        yield 'a class without its own is governed by its ancestors' => [$inherited, PathItemInheritedController::class, ['/api/v1', '/users']];

        yield 'interfaces are not followed' => [[PathItemContract::class, PathItemContractController::class], PathItemContractController::class, []];

        yield 'nothing above it carries one' => [[PathItemBaseController::class], PathItemPlainController::class, []];

        yield 'a class-string is a name, not a promise it loads' => [[PathItemBaseController::class], 'No\\Such\\Class', []];

        yield 'no class at all' => [[PathItemBaseController::class], null, []];
    }

    /**
     * @param list<class-string> $classes
     * @param list<string>       $expectedPrefixes
     */
    #[DataProvider('chainProvider')]
    public function testChainAndGoverning(array $classes, ?string $className, array $expectedPrefixes): void
    {
        $hierarchy = $this->assemble(...$classes)->buildPathItemHierarchy();

        $chain = $hierarchy->chain($className);

        $this->assertSame(
            $expectedPrefixes,
            array_map(static fn (OA\PathItem $pathItem): ?string => $pathItem->prefix, $chain),
        );
        $this->assertSame(
            $chain === [] ? null : $chain[count($chain) - 1],
            $hierarchy->governing($className),
            'governing is the last link in the chain',
        );
    }

    public function testEveryPathItemIsIndexedByItsOwnClass(): void
    {
        $hierarchy = $this->assemble(
            PathItemBaseController::class,
            PathItemUserController::class,
            PathItemContract::class,
        )->buildPathItemHierarchy();

        $this->assertSame(
            [PathItemBaseController::class, PathItemUserController::class, PathItemContract::class],
            array_keys($hierarchy->classes()),
            'an interface is indexed like any other class — it just never governs one',
        );
    }

    public function testAnItemWithoutAReflectorIsNotIndexed(): void
    {
        $specification = $this->assemble(PathItemBaseController::class);
        $specification->add(new OA\PathItem(summary: 'hand-written, no reflector'));

        $this->assertCount(1, $specification->buildPathItemHierarchy()->classes());
    }

    public static function pathProvider(): \Generator
    {
        $threeLevel = [PathItemGrandparentController::class, PathItemMiddleController::class, PathItemLeafController::class];
        $inherited = [PathItemBaseController::class, PathItemUserController::class, PathItemInheritedController::class];

        yield 'every link composes, outermost first' => [$threeLevel, PathItemLeafController::class, '/{id}', '/api/v2/orders', '/api/v2/orders/{id}'];

        yield 'an empty path is the prefix alone' => [$threeLevel, PathItemLeafController::class, '', '/api/v2/orders', '/api/v2/orders'];

        yield 'a class without its own composes its ancestors' => [$inherited, PathItemInheritedController::class, '/{id}/roles', '/api/v1/users', '/api/v1/users/{id}/roles'];

        yield 'a chain with no prefix leaves the path alone' => [[PathItemPlainController::class], PathItemPlainController::class, '/products/{id}', '', '/products/{id}'];
    }

    #[DataProvider('pathProvider')]
    public function testPrefixAndPathFor(array $classes, string $className, string $declaredPath, string $expectedPrefix, string $expectedPath): void
    {
        $specification = $this->assemble(...$classes);
        $hierarchy = $specification->buildPathItemHierarchy();

        $operations = array_values(array_filter(
            $specification->operations,
            static fn (OA\Operation $operation): bool => $operation->getClassName() === $className && $operation->path === $declaredPath,
        ));
        $this->assertCount(1, $operations);

        $this->assertSame($expectedPrefix, $hierarchy->prefixFor($operations[0]));
        $this->assertSame($expectedPath, $hierarchy->pathFor($operations[0]));
    }

    public function testAnOperationWithoutAClassOrAPathHasNoPrefixAndNoPath(): void
    {
        $hierarchy = $this->assemble(PathItemBaseController::class)->buildPathItemHierarchy();

        $operation = new OA\Operation\Get();

        $this->assertSame('', $hierarchy->prefixFor($operation));
        $this->assertNull($hierarchy->pathFor($operation));
    }

    public function testForOperationUsesTheDeclaringClass(): void
    {
        $specification = $this->assemble(PathItemBaseController::class, PathItemUserController::class);
        $hierarchy = $specification->buildPathItemHierarchy();

        $this->assertSame(
            $hierarchy->chain(PathItemUserController::class),
            $hierarchy->forOperation($specification->operations[0]),
        );
    }
}
