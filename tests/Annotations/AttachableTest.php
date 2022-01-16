<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Analysis;
use OpenApi\Annotations\Attachable;
use OpenApi\Annotations\Schema;
use OpenApi\Generator;
use OpenApi\Tests\Fixtures\Annotations\CustomAttachable;
use OpenApi\Tests\OpenApiTestCase;

class AttachableTest extends OpenApiTestCase
{
    public function testAttachablesAreAttached(): void
    {
        $analysis = $this->analysisFromFixtures(['UsingVar.php']);

        $schemas = $analysis->getAnnotationsOfType(Schema::class, true);

        $this->assertCount(2, $schemas[0]->attachables);
        $this->assertInstanceOf(Attachable::class, $schemas[0]->attachables[0]);
    }

    public function testCustomAttachableImplementationsAreAttached(): void
    {
        $analysis = new Analysis([], $this->getContext());
        (new Generator())
            ->addAlias('oaf', 'OpenApi\Tests\Fixtures\Annotations')
            ->addNamespace('OpenApi\Tests\Fixtures\Annotations\\')
            ->generate($this->fixtures(['UsingCustomAttachables.php']), $analysis);

        $schemas = $analysis->getAnnotationsOfType(Schema::class, true);

        $this->assertCount(2, $schemas[0]->attachables);
        $this->assertInstanceOf(CustomAttachable::class, $schemas[0]->attachables[0]);
    }
}
