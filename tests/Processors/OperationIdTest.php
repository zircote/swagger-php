<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Annotations as OA;
use OpenApi\Tests\OpenApiTestCase;

class OperationIdTest extends OpenApiTestCase
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

        $this->assertSame('entity/{id}', $operations[0]->path);
        $this->assertInstanceOf(OA\Get::class, $operations[0]);
        $this->assertSame('GET::entity/{id}::OpenApi\Tests\Fixtures\Processors\EntityControllerClass::getEntry', $operations[0]->operationId);

        $this->assertSame('entity/{id}', $operations[1]->path);
        $this->assertInstanceOf(OA\Post::class, $operations[1]);
        $this->assertSame('POST::entity/{id}::OpenApi\Tests\Fixtures\Processors\EntityControllerInterface::updateEntity', $operations[1]->operationId);

        $this->assertSame('entities/{id}', $operations[2]->path);
        $this->assertInstanceOf(OA\Delete::class, $operations[2]);
        $this->assertSame('DELETE::entities/{id}::OpenApi\Tests\Fixtures\Processors\EntityControllerTrait::deleteEntity', $operations[2]->operationId);
    }
}
