<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Annotations as OA;
use OpenApi\Tests\OpenApiTestCase;
use OpenApi\Tests\Fixtures\Processors\EntityControllerClass;
use OpenApi\Tests\Fixtures\Processors\EntityControllerInterface;
use OpenApi\Tests\Fixtures\Processors\EntityControllerTrait;

final class OperationIdTest extends OpenApiTestCase
{
    public function testGeneratedOperationId(): void
    {
        $analysis = $this->analysisFromFixtures(
            [
                'Processors/EntityControllerClass.php',
                'Processors/EntityControllerInterface.php',
                'Processors/EntityControllerTrait.php',
            ],
            $this->processorPipeline(),
            config: ['operationId' => ['hash' => false]]
        );

        $operations = $analysis->getAnnotationsOfType(OA\Operation::class);

        $this->assertCount(3, $operations);

        $this->assertSame('/entities/{id}', $operations[0]->path);
        $this->assertInstanceOf(OA\Get::class, $operations[0]);
        $this->assertSame('GET::/entities/{id}::' . EntityControllerClass::class . '::getEntry', $operations[0]->operationId);

        $this->assertSame('/entities/{id}', $operations[1]->path);
        $this->assertInstanceOf(OA\Post::class, $operations[1]);
        $this->assertSame('POST::/entities/{id}::' . EntityControllerInterface::class . '::updateEntity', $operations[1]->operationId);

        $this->assertSame('/entities/{id}', $operations[2]->path);
        $this->assertInstanceOf(OA\Delete::class, $operations[2]);
        $this->assertSame('DELETE::/entities/{id}::' . EntityControllerTrait::class . '::deleteEntity', $operations[2]->operationId);
    }
}
