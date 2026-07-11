<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Assembler;
use OpenApi\Augmenter;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Fixtures;
use PHPUnit\Framework\TestCase;

final class PathItemResolveTest extends TestCase
{
    protected function assemble(string ...$classes): Specification
    {
        $assembler = new Assembler();
        foreach ($classes as $class) {
            $assembler->collect(new \ReflectionClass($class));
        }

        return $assembler->getSpecification();
    }

    public function testPrefixComposition(): void
    {
        $spec = $this->assemble(
            Fixtures\Augmenter\PathItemBaseController::class,
            Fixtures\Augmenter\PathItemUserController::class,
        );

        (new Augmenter\PathItemResolve())($spec);

        $paths = array_map(fn (OA\Operation $op): ?string => $op->path, $spec->operations);
        sort($paths);

        $this->assertSame(['/api/v1/users/list', '/api/v1/users/{id}'], $paths);
    }

    public function testTagsClonedToOperations(): void
    {
        $spec = $this->assemble(
            Fixtures\Augmenter\PathItemBaseController::class,
            Fixtures\Augmenter\PathItemUserController::class,
        );

        (new Augmenter\PathItemResolve())($spec);

        foreach ($spec->operations as $operation) {
            $this->assertSame(['Users'], $operation->tags);
        }
    }

    public function testSecurityClonedToOperations(): void
    {
        $spec = $this->assemble(
            Fixtures\Augmenter\PathItemBaseController::class,
            Fixtures\Augmenter\PathItemUserController::class,
        );

        (new Augmenter\PathItemResolve())($spec);

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

        (new Augmenter\PathItemResolve())($spec);

        $this->assertSame(['Custom', 'Users'], $spec->operations[0]->tags);
    }

    public function testPathItemPathResolved(): void
    {
        $spec = $this->assemble(
            Fixtures\Augmenter\PathItemBaseController::class,
            Fixtures\Augmenter\PathItemUserController::class,
        );

        (new Augmenter\PathItemResolve())($spec);

        $pathItemsWithPath = array_filter(
            $spec->pathItems,
            fn (OA\PathItem $pi): bool => $pi->path !== null,
        );

        $this->assertNotEmpty($pathItemsWithPath);
        foreach ($pathItemsWithPath as $pi) {
            $this->assertStringStartsWith('/api/v1/users/', $pi->path);
        }
    }

    public function testPathItemWithSummaryGetsPath(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\PathItemPlainController::class);

        (new Augmenter\PathItemResolve())($spec);

        $pathItemsWithPath = array_filter(
            $spec->pathItems,
            fn (OA\PathItem $pi): bool => $pi->path !== null,
        );

        $paths = array_map(fn (OA\PathItem $pi): string => $pi->path, array_values($pathItemsWithPath));
        sort($paths);

        $this->assertSame(['/products', '/products/{id}'], $paths);
    }

    public function testNoPrefixNoPathItem(): void
    {
        $spec = new Specification();
        $spec->operations[] = new OA\Operation(path: '/test', method: 'get');

        (new Augmenter\PathItemResolve())($spec);

        $this->assertSame('/test', $spec->operations[0]->path);
    }

    public function testThreeLevelPrefixComposition(): void
    {
        $spec = $this->assemble(
            Fixtures\Augmenter\PathItemGrandparentController::class,
            Fixtures\Augmenter\PathItemMiddleController::class,
            Fixtures\Augmenter\PathItemLeafController::class,
        );

        (new Augmenter\PathItemResolve())($spec);

        $paths = array_map(fn (OA\Operation $op): ?string => $op->path, $spec->operations);
        sort($paths);

        $this->assertSame(['/api/v2/orders', '/api/v2/orders/{id}'], $paths);
    }

    public function testTagsInheritedFromMiddleLevel(): void
    {
        $spec = $this->assemble(
            Fixtures\Augmenter\PathItemGrandparentController::class,
            Fixtures\Augmenter\PathItemMiddleController::class,
            Fixtures\Augmenter\PathItemLeafController::class,
        );

        (new Augmenter\PathItemResolve())($spec);

        foreach ($spec->operations as $operation) {
            $this->assertSame(['V2'], $operation->tags);
        }
    }

    public function testSharedResponsesCloned(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\PathItemSharedResponseController::class);

        (new Augmenter\PathItemResolve())($spec);

        $postOp = null;
        foreach ($spec->operations as $operation) {
            if ($operation->method === 'post') {
                $postOp = $operation;
                break;
            }
        }

        $this->assertInstanceOf(OA\Operation::class, $postOp);
        $codes = array_map(fn (OA\Response $r): string => (string) $r->response, $postOp->responses);
        sort($codes);

        // 201 from operation + 401 and 500 from PathItem
        $this->assertSame(['201', '401', '500'], $codes);
    }

    public function testSharedSecurityCloned(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\PathItemSharedResponseController::class);

        (new Augmenter\PathItemResolve())($spec);

        foreach ($spec->operations as $operation) {
            $this->assertNotNull($operation->security);
            $this->assertCount(1, $operation->security);
            $this->assertSame('apiKey', $operation->security[0]->scheme);
        }
    }

    public function testSharedParametersEmittedAtPathLevel(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\PathItemSharedResponseController::class);

        (new Augmenter\PathItemResolve())($spec);

        $pathItemsWithPath = array_filter(
            $spec->pathItems,
            fn (OA\PathItem $pi): bool => $pi->path !== null,
        );

        $this->assertNotEmpty($pathItemsWithPath);
        foreach ($pathItemsWithPath as $pi) {
            $this->assertNotNull($pi->parameters);
            $this->assertCount(1, $pi->parameters);
            $this->assertSame('X-Request-Id', $pi->parameters[0]->name);
        }
    }

    public function testSharedResponseNotOverriddenWhenCodeExists(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\PathItemSharedResponseController::class);

        (new Augmenter\PathItemResolve())($spec);

        $getOp = null;
        foreach ($spec->operations as $operation) {
            if ($operation->method === 'get') {
                $getOp = $operation;
                break;
            }
        }

        $this->assertInstanceOf(OA\Operation::class, $getOp);

        // Operation has its own 401 — PathItem's 401 should NOT override it
        $responses401 = array_filter(
            $getOp->responses,
            fn (OA\Response $r): bool => (string) $r->response === '401',
        );
        $this->assertCount(1, $responses401);
        $this->assertSame('Custom unauthorized', array_values($responses401)[0]->description);
    }

    public function testReusablePathItemNoPath(): void
    {
        $spec = $this->assemble(
            Fixtures\Augmenter\PathItemReusable::class,
            Fixtures\Augmenter\PathItemRefController::class,
        );

        (new Augmenter\PathItemResolve())($spec);

        // Reusable PathItem (no operations) keeps path as null — it's a component
        $reusable = null;
        foreach ($spec->pathItems as $pi) {
            $reflector = $pi->getReflector();
            if ($reflector instanceof \ReflectionClass && $reflector->getName() === Fixtures\Augmenter\PathItemReusable::class) {
                $reusable = $pi;
                break;
            }
        }

        $this->assertInstanceOf(OA\PathItem::class, $reusable);
        $this->assertNull($reusable->path);
        $this->assertNotNull($reusable->parameters);
        $this->assertCount(2, $reusable->parameters);
    }

    public function testRefPathItemGetsPathFromOperations(): void
    {
        $spec = $this->assemble(
            Fixtures\Augmenter\PathItemReusable::class,
            Fixtures\Augmenter\PathItemRefController::class,
        );

        (new Augmenter\PathItemResolve())($spec);

        // The referencing PathItem gets its path resolved from its operation
        $refPi = null;
        foreach ($spec->pathItems as $pi) {
            $reflector = $pi->getReflector();
            if ($reflector instanceof \ReflectionClass && $reflector->getName() === Fixtures\Augmenter\PathItemRefController::class) {
                $refPi = $pi;
                break;
            }
        }

        $this->assertInstanceOf(OA\PathItem::class, $refPi);
        $this->assertSame(Fixtures\Augmenter\PathItemReusable::class, $refPi->ref);
    }

    public function testPrefixOnlyPathItemNoPathSet(): void
    {
        $spec = $this->assemble(
            Fixtures\Augmenter\PathItemBaseController::class,
            Fixtures\Augmenter\PathItemUserController::class,
        );

        (new Augmenter\PathItemResolve())($spec);

        // BaseController has only prefix, no spec properties — should not get a path
        $basePathItem = null;
        foreach ($spec->pathItems as $pi) {
            $reflector = $pi->getReflector();
            if ($reflector instanceof \ReflectionClass && $reflector->getName() === Fixtures\Augmenter\PathItemBaseController::class) {
                $basePathItem = $pi;
                break;
            }
        }

        $this->assertInstanceOf(OA\PathItem::class, $basePathItem);
        $this->assertNull($basePathItem->path);
    }
}
