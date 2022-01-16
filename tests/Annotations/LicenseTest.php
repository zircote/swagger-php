<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Annotations\OpenApi;
use OpenApi\Tests\OpenApiTestCase;

class LicenseTest extends OpenApiTestCase
{
    public function testValidation3_0_0(): void
    {
        $annotations = $this->annotationsFromDocBlockParser('@OA\License(name="MIT", identifier="MIT", url="http://localhost")', [], OpenApi::VERSION_3_0_0);
        $annotations[0]->validate();
    }

    public function testValidation3_1_0(): void
    {
        $this->assertOpenApiLogEntryContains('@OA\License() url and identifier are mutually exclusive');

        $annotations = $this->annotationsFromDocBlockParser('@OA\License(name="MIT", identifier="MIT", url="http://localhost")', [], OpenApi::VERSION_3_1_0);
        $annotations[0]->validate();
    }
}
