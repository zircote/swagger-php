<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Annotations as OA;
use OpenApi\Tests\OpenApiTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class OpenApiTest extends OpenApiTestCase
{
    public function testValidVersion(): void
    {
        $this->assertOpenApiLogEntryContains('Required @OA\PathItem() not found');
        $this->assertOpenApiLogEntryContains('Required @OA\Info() not found');

        $openapi = new OA\OpenApi(['_context' => $this->getContext()]);
        $openapi->openapi = '3.0.3';
        $openapi->validate();
    }

    public function testValidVersion310(): void
    {
        $this->assertOpenApiLogEntryContains("At least one of 'Required @OA\PathItem(), @OA\Components() or @OA\Webhook() not found'");

        $openapi = new OA\OpenApi(['_context' => $this->getContext()]);
        $openapi->openapi = '3.1.1';
        $openapi->validate();
    }

    public function testInvalidVersion(): void
    {
        $this->assertOpenApiLogEntryContains('Unsupported OpenAPI version "2". Allowed versions are:');

        $openapi = new OA\OpenApi(['_context' => $this->getContext()]);
        $openapi->openapi = '2';
        $openapi->validate();
    }

    public function testSerialize(): void
    {
        $openapi = $this->analysisFromFixtures(['Customer.php'])->openapi;
        $unserialized = unserialize(serialize($openapi));

        $this->assertSpecEquals($openapi, $unserialized);
    }

    public static function versionMatchProvider(): iterable
    {
        yield '3.0.0-3.0.0' => ['3.0.0', '3.0.0', true];
        yield '3.0.0-3.0.x' => ['3.0.0', '3.0.x', true];
        yield '3.0.3-3.0.x' => ['3.0.3', '3.0.x', true];
        yield '3.0.3-3.1.x' => ['3.0.3', '3.1.x', false];
    }

    #[DataProvider('versionMatchProvider')]
    public function testVersionMatch(string $given, string $compare, bool $expected): void
    {
        $this->assertEquals($expected, OA\OpenApi::versionMatch($given, $compare));
    }
}
