<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Annotations as OA;
use OpenApi\Processors\OperationId;
use OpenApi\Tests\OpenApiTestCase;
use OpenApi\Tests\Fixtures\Processors\EntityControllerClass;
use OpenApi\Tests\Fixtures\Processors\EntityControllerInterface;
use OpenApi\Tests\Fixtures\Processors\EntityControllerTrait;

final class OperationIdTest extends OpenApiTestCase
{
    public function testGeneratedOperationId(): void
    {
        $analysis = $this->analysisFromFixtures([
            'Processors/EntityControllerClass.php',
            'Processors/EntityControllerInterface.php',
            'Processors/EntityControllerTrait.php',
        ]);
        $analysis->process([new OperationId(false)]);
        /** @var OA\Operation[] $operations */
        $operations = $analysis->getAnnotationsOfType(OA\Operation::class);

        $this->assertCount(3, $operations);

        $this->assertSame('entity/{id}', $operations[0]->path);
        $this->assertInstanceOf(OA\Get::class, $operations[0]);
<<<<<<< HEAD
        $this->assertSame('GET::entity/{id}::OpenApi\Tests\Fixtures\Processors\EntityControllerClass::getEntry', $operations[0]->operationId);
=======
        $this->assertSame('GET::/entities/{id}::' . EntityControllerClass::class . '::getEntry', $operations[0]->operationId);
>>>>>>> 09b3543 (Subject examples and tests to rector rules (#1942))

        $this->assertSame('entity/{id}', $operations[1]->path);
        $this->assertInstanceOf(OA\Post::class, $operations[1]);
<<<<<<< HEAD
        $this->assertSame('POST::entity/{id}::OpenApi\Tests\Fixtures\Processors\EntityControllerInterface::updateEntity', $operations[1]->operationId);
=======
        $this->assertSame('POST::/entities/{id}::' . EntityControllerInterface::class . '::updateEntity', $operations[1]->operationId);
>>>>>>> 09b3543 (Subject examples and tests to rector rules (#1942))

        $this->assertSame('entities/{id}', $operations[2]->path);
        $this->assertInstanceOf(OA\Delete::class, $operations[2]);
<<<<<<< HEAD
        $this->assertSame('DELETE::entities/{id}::OpenApi\Tests\Fixtures\Processors\EntityControllerTrait::deleteEntity', $operations[2]->operationId);
=======
        $this->assertSame('DELETE::/entities/{id}::' . EntityControllerTrait::class . '::deleteEntity', $operations[2]->operationId);
>>>>>>> 09b3543 (Subject examples and tests to rector rules (#1942))
    }
}
