<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Analysis;
use OpenApi\Annotations\Schema;
use OpenApi\Generator;
use OpenApi\Tests\OpenApiTestCase;

class AttributeTest extends OpenApiTestCase
{
    public function testAttributeAreAttached()
    {
        $analysis = $this->analysisFromFixtures('UsingVar.php');
        $schemas = $analysis->getAnnotationsOfType(Schema::class, true);
        $this->assertCount(2, $schemas[0]->attributes);
    }

    public function testCustomAttributeImplementationsAreAttached()
    {
        $analysis = new Analysis([], $this->getContext());
        (new Generator())
            //->setAliases(['oaf' => 'OpenApi\\Tests\\Annotations'])
            ->setNamespaces(['OpenApi\\Tests\\Annotations\\'])
            ->generate($this->fixtures('UsingCustomAttributes.php'), $analysis);

        $schemas = $analysis->getAnnotationsOfType(Schema::class, true);
        $this->assertCount(2, $schemas[0]->attributes);
    }
}
