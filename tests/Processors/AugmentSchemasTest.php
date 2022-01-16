<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Generator;
use OpenApi\Processors\AugmentSchemas;
use OpenApi\Processors\MergeIntoComponents;
use OpenApi\Processors\MergeIntoOpenApi;
use OpenApi\Tests\OpenApiTestCase;

class AugmentSchemasTest extends OpenApiTestCase
{
    public function testAugmentSchemas(): void
    {
        $analysis = $this->analysisFromFixtures(['Customer.php']);
        $analysis->process([
            // create openapi->components
            new MergeIntoOpenApi(),
            // Merge standalone Scheme's into openapi->components
            new MergeIntoComponents(),
        ]);

        $this->assertCount(1, $analysis->openapi->components->schemas);
        $customer = $analysis->openapi->components->schemas[0];
        $this->assertSame(Generator::UNDEFINED, $customer->schema, 'Sanity check. No scheme was defined');
        $this->assertSame(Generator::UNDEFINED, $customer->properties, 'Sanity check. @OA\Property\'s not yet merged ');
        $analysis->process([new AugmentSchemas()]);

        $this->assertSame('Customer', $customer->schema, '@OA\Schema()->schema based on classname');
        $this->assertCount(10, $customer->properties, '@OA\Property()s are merged into the @OA\Schema of the class');
    }

    public function testAugmentSchemasForInterface(): void
    {
        $analysis = $this->analysisFromFixtures(['CustomerInterface.php']);
        $analysis->process([
            // create openapi->components
            new MergeIntoOpenApi(),
            // Merge standalone Scheme's into openapi->components
            new MergeIntoComponents(),
        ]);

        $this->assertCount(1, $analysis->openapi->components->schemas);
        $customer = $analysis->openapi->components->schemas[0];
        $this->assertSame(Generator::UNDEFINED, $customer->properties, 'Sanity check. @OA\Property\'s not yet merged ');
        $analysis->process([new AugmentSchemas()]);

        $this->assertCount(9, $customer->properties, '@OA\Property()s are merged into the @OA\Schema of the class');
    }
}
