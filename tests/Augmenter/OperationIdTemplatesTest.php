<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Fixtures\OperationMethods\PetController;
use OpenApi\Utils\Pipeline;
use PHPUnit\Framework\TestCase;

final class OperationIdTemplatesTest extends TestCase
{
    public function testMethodIsReplaced(): void
    {
        $spec = new Specification();
        $spec->operations[] = new OA\Operation(path: '/pets', method: OA\HttpMethod::Get, operationId: '{method}Pets');
        $spec->operations[] = new OA\Operation(path: '/pets', method: OA\HttpMethod::Post, operationId: 'addPet');

        (new Augmenter\OperationIdTemplates())($spec);

        $this->assertSame(['getPets', 'addPet'], array_map(static fn (OA\Operation $operation): ?string => $operation->operationId, $spec->operations));
    }

    public function testAnUnknownPlaceholderIsLeftAsWritten(): void
    {
        $spec = new Specification();
        $spec->operations[] = new OA\Operation(path: '/pets', method: OA\HttpMethod::Get, operationId: '{method}{path}');

        (new Augmenter\OperationIdTemplates())($spec);

        $this->assertSame('get{path}', $spec->operations[0]->operationId);
    }

    /**
     * Generated ids are optional; the template is the author's, so it is expanded either way.
     */
    public function testExpandedWithoutOperationIds(): void
    {
        $paths = (new Builder())
            ->setMode(Mode::SPEC)
            ->withAugmenters(static fn (Pipeline $augmenters): Pipeline => $augmenters->remove(Augmenter\OperationIds::class))
            ->addSource(new \ReflectionClass(PetController::class))
            ->build()
            ->toArray()['paths'];

        $this->assertSame('getPet', $paths['/pets/{id}']['get']['operationId']);
        $this->assertSame('headPet', $paths['/pets/{id}']['head']['operationId']);
    }
}
