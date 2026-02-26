<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Tests\OpenApiTestCase;

final class LicenseTest extends OpenApiTestCase
{
    public function testValidation3_0_0(): void
    {
        $annotations = $this->annotationsFromDocBlockParser('@OA\License(name="MIT", identifier="MIT", url="http://localhost")', [], '3.0.4');
        $this->validateSingle($annotations[0]);
    }

    public function testValidation3_1_0(): void
    {
        $this->assertOpenApiLogEntryContains('At least one of @OA\PathItem(), @OA\Components() or @OA\Webhook() required');
        $this->assertOpenApiLogEntryContains('@OA\License() url and identifier are mutually exclusive');

        $annotations = $this->annotationsFromDocBlockParser('@OA\OpenApi(openapi="3.1.1", @OA\Info(title="foo", version="1.0.0", @OA\License(name="MIT", identifier="MIT", url="http://localhost")))', version: '3.1.1');
        $this->validateAnnotations($annotations, version: '3.1.1', raw: true);
    }
}
