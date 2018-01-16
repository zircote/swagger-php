<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Processors\AugmentProperties;
use Swagger\Processors\AugmentSchemas;
use Swagger\Processors\MergeIntoOpenApi;
use Swagger\Processors\MergeIntoComponents;
use Swagger\StaticAnalyser;

class AugmentSchemasTest extends SwaggerTestCase
{
    public function testAugmentSchemas()
    {
        $analyser = new StaticAnalyser();
        $analysis = $analyser->fromFile(__DIR__ . '/Fixtures/Customer.php');
        $analysis->process(new MergeIntoOpenApi()); // create openapi->components
        $analysis->process(new MergeIntoComponents()); // Merge standalone Scheme's into openapi->components
        $this->assertCount(1, $analysis->openapi->components->schemas);
        $customer = $analysis->openapi->components->schemas[0];
        $this->assertNull($customer->schema, 'Sanity check. No scheme was defined');
        $this->assertNull($customer->properties, 'Sanity check. @OAS\Property\'s not yet merged ');
        $analysis->process(new AugmentSchemas());
        $this->assertSame('Customer', $customer->schema, '@OAS\Schema()->schema based on classname');
        $this->assertCount(5, $customer->properties, '@OAS\Property()s are merged into the @OAS\Schema of the class');
    }
}
