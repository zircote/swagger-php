<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;
use OpenApi\Pipeline;
use OpenApi\Processors\CleanUnusedComponents;
use OpenApi\Tests\Fixtures\Annotations\CustomAttachable;
use OpenApi\Tests\OpenApiTestCase;

class AttachableTest extends OpenApiTestCase
{
    public function testAttachablesAreAttached(): void
    {
        $analysis = $this->analysisFromFixtures(['UsingVar.php']);

        $schemas = $analysis->getAnnotationsOfType(OA\Schema::class, true);

        $this->assertCount(2, $schemas[0]->attachables);
        $this->assertInstanceOf(OA\Attachable::class, $schemas[0]->attachables[0]);
    }

    public function testCustomAttachableImplementationsAreAttached(): void
    {
        $analysis = new Analysis([], $this->getContext());
        (new Generator())
            ->setTypeResolver($this->getTypeResolver())
            ->addAlias('oaf', 'OpenApi\Tests\Fixtures\Annotations')
            ->addNamespace('OpenApi\Tests\Fixtures\Annotations\\')
            ->withProcessorPipeline(function (Pipeline $processor) { $processor->remove(null, function ($pipe) { return !$pipe instanceof CleanUnusedComponents; }); })
            ->generate($this->fixtures(['UsingCustomAttachables.php']), $analysis);

        $schemas = $analysis->getAnnotationsOfType(OA\Schema::class, true);

        $this->assertCount(2, $schemas[0]->attachables);
        $this->assertInstanceOf(CustomAttachable::class, $schemas[0]->attachables[0]);
    }
}
