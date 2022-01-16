<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Annotations\OpenApi;
use OpenApi\Tests\OpenApiTestCase;

class OpenApiTest extends OpenApiTestCase
{
    public function testValidVersion(): void
    {
        $this->assertOpenApiLogEntryContains('Required @OA\Info() not found');
        $this->assertOpenApiLogEntryContains('Required @OA\PathItem() not found');

        $openapi = new OpenApi(['_context' => $this->getContext()]);
        $openapi->openapi = '3.0.0';
        $openapi->validate();
    }

    public function testInvalidVersion(): void
    {
        $this->assertOpenApiLogEntryContains('Unsupported OpenAPI version "2". Allowed versions are: 3.0.0, 3.1.0');

        $openapi = new OpenApi(['_context' => $this->getContext()]);
        $openapi->openapi = '2';
        $openapi->validate();
    }
}
