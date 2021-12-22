<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Tests\OpenApiTestCase;

class LicenseTest extends OpenApiTestCase
{
    public function testValidation()
    {
        $annotations = $this->annotationsFromDocBlockParser('@OA\License(name="MIT", identifier="MIT", url="http://localhost")');
        $this->assertOpenApiLogEntryContains('@OA\License() url and identifier are mutually exclusive');
        $annotations[0]->validate();
    }
}
