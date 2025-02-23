<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Annotations as OA;
use OpenApi\Tests\OpenApiTestCase;

class OpenApiTest extends OpenApiTestCase
{
    public function testValidVersion(): void
    {
        $this->assertOpenApiLogEntryContains('Required @OA\PathItem() not found');
        $this->assertOpenApiLogEntryContains('Required @OA\Info() not found');

        $openapi = new OA\OpenApi(['_context' => $this->getContext()]);
        $openapi->openapi = OA\OpenApi::VERSION_3_0_0;
        $openapi->validate();
    }

    public function testValidVersion310(): void
    {
        $this->assertOpenApiLogEntryContains("At least one of 'Required @OA\PathItem(), @OA\Components() or @OA\Webhook() not found'");

        $openapi = new OA\OpenApi(['_context' => $this->getContext()]);
        $openapi->openapi = OA\OpenApi::VERSION_3_1_0;
        $openapi->validate();
    }

    public function testInvalidVersion(): void
    {
        $this->assertOpenApiLogEntryContains('Unsupported OpenAPI version "2". Allowed versions are: 3.0.0, 3.1.0');

        $openapi = new OA\OpenApi(['_context' => $this->getContext()]);
        /** @phpstan-ignore assign.propertyType */
        $openapi->openapi = '2';
        $openapi->validate();
    }
}
