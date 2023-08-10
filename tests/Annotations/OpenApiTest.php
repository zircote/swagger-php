<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Annotations as OA;
use OpenApi\Tests\OpenApiTestCase;

class OpenApiTest extends OpenApiTestCase
{
    public function testValidVersion310(): void
    {
        $this->assertOpenApiLogEntryContains('The OpenAPI document must contain at least one paths field, a components field or a webhooks field');

        $openapi = new OA\OpenApi(['_context' => $this->getContext()]);
        $openapi->openapi = '3.1.0';
        $openapi->validate();
    }

    public function testValidVersion300(): void
    {
        $this->assertOpenApiLogEntryContains('The OpenAPI document must contain paths field');

        $openapi = new OA\OpenApi(['_context' => $this->getContext()]);
        $openapi->openapi = '3.0.0';
        $openapi->validate();
    }

    public function testInvalidVersion(): void
    {
        $this->assertOpenApiLogEntryContains('Unsupported OpenAPI version "2". Allowed versions are: 3.0.0, 3.1.0');

        $openapi = new OA\OpenApi(['_context' => $this->getContext()]);
        $openapi->openapi = '2';
        $openapi->validate();
    }
}
