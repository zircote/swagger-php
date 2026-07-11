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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PathItemResolveTest extends TestCase
{
    use AssemblesSpecification;

    private function resolve(Specification $spec): Specification
    {
        (new Augmenter\PathItemResolve())($spec);

        return $spec;
    }

    private function findPathItemByClass(Specification $spec, string $class): ?OA\PathItem
    {
        foreach ($spec->pathItems as $pi) {
            $reflector = $pi->getReflector();
            if ($reflector instanceof \ReflectionClass && $reflector->getName() === $class) {
                return $pi;
            }
        }

        return null;
    }

    private function findOperationByMethod(Specification $spec, string $method): ?OA\Operation
    {
        foreach ($spec->operations as $operation) {
            if ($operation->method === $method) {
                return $operation;
            }
        }

        return null;
    }

    public static function prefixCompositionProvider(): \Generator
    {
        yield '2-level' => [
            [Fixtures\Augmenter\PathItemBaseController::class, Fixtures\Augmenter\PathItemUserController::class],
            ['/api/v1/users/list', '/api/v1/users/{id}'],
        ];

        yield '3-level' => [
            [Fixtures\Augmenter\PathItemGrandparentController::class, Fixtures\Augmenter\PathItemMiddleController::class, Fixtures\Augmenter\PathItemLeafController::class],
            ['/api/v2/orders', '/api/v2/orders/{id}'],
        ];
    }

    #[DataProvider('prefixCompositionProvider')]
    public function testPrefixComposition(array $classes, array $expectedPaths): void
    {
        $spec = $this->resolve($this->assemble(...$classes));

        $paths = array_map(fn (OA\Operation $op): ?string => $op->path, $spec->operations);
        sort($paths);

        $this->assertSame($expectedPaths, $paths);
    }

    public static function tagsProvider(): \Generator
    {
        yield 'direct' => [
            [Fixtures\Augmenter\PathItemBaseController::class, Fixtures\Augmenter\PathItemUserController::class],
            ['Users'],
        ];

        yield 'inherited from middle' => [
            [Fixtures\Augmenter\PathItemGrandparentController::class, Fixtures\Augmenter\PathItemMiddleController::class, Fixtures\Augmenter\PathItemLeafController::class],
            ['V2'],
        ];
    }

    #[DataProvider('tagsProvider')]
    public function testTagsClonedToOperations(array $classes, array $expectedTags): void
    {
        $spec = $this->resolve($this->assemble(...$classes));

        foreach ($spec->operations as $operation) {
            $this->assertSame($expectedTags, $operation->tags);
        }
    }

    public function testSecurityClonedToOperations(): void
    {
        $spec = $this->resolve($this->assemble(
            Fixtures\Augmenter\PathItemBaseController::class,
            Fixtures\Augmenter\PathItemUserController::class,
        ));

        foreach ($spec->operations as $operation) {
            $this->assertNotNull($operation->security);
            $this->assertCount(1, $operation->security);
            $this->assertSame('bearerAuth', $operation->security[0]->scheme);
        }
    }

    public function testExplicitTagsMergedAdditively(): void
    {
        $spec = $this->assemble(
            Fixtures\Augmenter\PathItemBaseController::class,
            Fixtures\Augmenter\PathItemUserController::class,
        );
        $spec->operations[0]->tags = ['Custom'];

        $this->resolve($spec);

        $this->assertSame(['Custom', 'Users'], $spec->operations[0]->tags);
    }

    public function testPathItemPathResolved(): void
    {
        $spec = $this->resolve($this->assemble(
            Fixtures\Augmenter\PathItemBaseController::class,
            Fixtures\Augmenter\PathItemUserController::class,
        ));

        $pathItemsWithPath = array_filter($spec->pathItems, fn (OA\PathItem $pi): bool => $pi->path !== null);

        $this->assertNotEmpty($pathItemsWithPath);
        foreach ($pathItemsWithPath as $pi) {
            $this->assertStringStartsWith('/api/v1/users/', $pi->path);
        }
    }

    public function testPathItemWithSummaryGetsPath(): void
    {
        $spec = $this->resolve($this->assemble(Fixtures\Augmenter\PathItemPlainController::class));

        $paths = array_map(
            fn (OA\PathItem $pi): string => $pi->path,
            array_values(array_filter($spec->pathItems, fn (OA\PathItem $pi): bool => $pi->path !== null)),
        );
        sort($paths);

        $this->assertSame(['/products', '/products/{id}'], $paths);
    }

    public function testNoPrefixNoPathItem(): void
    {
        $spec = new Specification();
        $spec->operations[] = new OA\Operation(path: '/test', method: 'get');

        $this->resolve($spec);

        $this->assertSame('/test', $spec->operations[0]->path);
    }

    public function testSharedResponsesCloned(): void
    {
        $spec = $this->resolve($this->assemble(Fixtures\Augmenter\PathItemSharedResponseController::class));

        $postOp = $this->findOperationByMethod($spec, 'post');
        $this->assertInstanceOf(OA\Operation::class, $postOp);

        $codes = array_map(fn (OA\Response $r): string => (string) $r->response, $postOp->responses);
        sort($codes);

        $this->assertSame(['201', '401', '500'], $codes);
    }

    public function testSharedSecurityCloned(): void
    {
        $spec = $this->resolve($this->assemble(Fixtures\Augmenter\PathItemSharedResponseController::class));

        foreach ($spec->operations as $operation) {
            $this->assertNotNull($operation->security);
            $this->assertCount(1, $operation->security);
            $this->assertSame('apiKey', $operation->security[0]->scheme);
        }
    }

    public function testSharedParametersEmittedAtPathLevel(): void
    {
        $spec = $this->resolve($this->assemble(Fixtures\Augmenter\PathItemSharedResponseController::class));

        $pathItemsWithPath = array_filter($spec->pathItems, fn (OA\PathItem $pi): bool => $pi->path !== null);

        $this->assertNotEmpty($pathItemsWithPath);
        foreach ($pathItemsWithPath as $pi) {
            $this->assertNotNull($pi->parameters);
            $this->assertCount(1, $pi->parameters);
            $this->assertSame('X-Request-Id', $pi->parameters[0]->name);
        }
    }

    public function testSharedResponseNotOverriddenWhenCodeExists(): void
    {
        $spec = $this->resolve($this->assemble(Fixtures\Augmenter\PathItemSharedResponseController::class));

        $getOp = $this->findOperationByMethod($spec, 'get');
        $this->assertInstanceOf(OA\Operation::class, $getOp);

        $responses401 = array_filter($getOp->responses, fn (OA\Response $r): bool => (string) $r->response === '401');
        $this->assertCount(1, $responses401);
        $this->assertSame('Custom unauthorized', array_values($responses401)[0]->description);
    }

    public function testReusablePathItemNoPath(): void
    {
        $spec = $this->resolve($this->assemble(
            Fixtures\Augmenter\PathItemReusable::class,
            Fixtures\Augmenter\PathItemRefController::class,
        ));

        $reusable = $this->findPathItemByClass($spec, Fixtures\Augmenter\PathItemReusable::class);
        $this->assertInstanceOf(OA\PathItem::class, $reusable);
        $this->assertNull($reusable->path);
        $this->assertNotNull($reusable->parameters);
        $this->assertCount(2, $reusable->parameters);
    }

    public function testRefPathItemGetsPathFromOperations(): void
    {
        $spec = $this->resolve($this->assemble(
            Fixtures\Augmenter\PathItemReusable::class,
            Fixtures\Augmenter\PathItemRefController::class,
        ));

        $refPi = $this->findPathItemByClass($spec, Fixtures\Augmenter\PathItemRefController::class);
        $this->assertInstanceOf(OA\PathItem::class, $refPi);
        $this->assertSame(Fixtures\Augmenter\PathItemReusable::class, $refPi->ref);
    }

    public function testPrefixOnlyPathItemNoPathSet(): void
    {
        $spec = $this->resolve($this->assemble(
            Fixtures\Augmenter\PathItemBaseController::class,
            Fixtures\Augmenter\PathItemUserController::class,
        ));

        $basePathItem = $this->findPathItemByClass($spec, Fixtures\Augmenter\PathItemBaseController::class);
        $this->assertInstanceOf(OA\PathItem::class, $basePathItem);
        $this->assertNull($basePathItem->path);
    }
}
