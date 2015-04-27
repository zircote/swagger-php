<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Annotations\Swagger;
use Swagger\Processing;

class ProcessingTest extends SwaggerTestCase {

    function testRegister() {
        $counter = 0;
        $swagger = $this->createSwaggerWithInfo();
        Processing::process($swagger);
        $this->assertSame(0, $counter);
        $countProcessor = function (Swagger $swagger) use (&$counter) {
            $counter++;
        };
        Processing::register($countProcessor);
        Processing::process($swagger);
        $this->assertSame(1, $counter);
        Processing::unregister($countProcessor);
        Processing::process($swagger);
        $this->assertSame(1, $counter);
    }

}
