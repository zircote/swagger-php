<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Builder;

use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Fixtures\Augmenter\RefTarget;
use PHPUnit\Framework\TestCase;

/**
 * `Builder::withDocument()` adds a hand-written document to what the sources describe, and the
 * attributes win wherever both describe the same thing.
 */
final class DocumentTest extends TestCase
{
    public function testADocumentAndTheSourcesMakeOneDocument(): void
    {
        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource(new \ReflectionClass(RefTarget::class))
            ->withDocument([
                'openapi' => '3.1.0',
                'info' => ['title' => 'Written', 'version' => '1.0.0'],
                'paths' => ['/targets' => ['get' => ['responses' => ['200' => [
                    'description' => 'A target',
                    'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/RefTarget']]],
                ]]]]],
            ])
            ->build();

        $document = $result->toArray();

        $this->assertSame('Written', $document['info']['title']);
        $this->assertSame('#/components/schemas/RefTarget', $document['paths']['/targets']['get']['responses'][200]['content']['application/json']['schema']['$ref']);
        $this->assertArrayHasKey('RefTarget', $document['components']['schemas']);
        $this->assertSame([], array_column($result->log(), 'message'));
    }

    public function testTheAttributesWinOverInfoAndServers(): void
    {
        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->withSpecification(fn (Specification $specification): Specification => $specification->add(
                new OA\Info(title: 'From attributes', version: '2.0.0'),
                new OA\Server(url: 'https://api.example.com'),
            ))
            ->withDocument([
                'openapi' => '3.1.0',
                'info' => ['title' => 'Written', 'version' => '1.0.0'],
                'servers' => [['url' => 'https://written.example.com']],
                'externalDocs' => ['url' => 'https://docs.example.com'],
            ])
            ->build();

        $document = $result->toArray();

        $this->assertSame(['title' => 'From attributes', 'version' => '2.0.0'], $document['info']);
        $this->assertSame([['url' => 'https://api.example.com']], $document['servers']);
        $this->assertSame(['url' => 'https://docs.example.com'], $document['externalDocs'], 'the attributes have none, so the document fills it');
    }

    public function testAComponentInBothKeepsTheAttributes(): void
    {
        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource(new \ReflectionClass(RefTarget::class))
            ->withDocument([
                'openapi' => '3.1.0',
                'info' => ['title' => 'Written', 'version' => '1.0.0'],
                'paths' => ['/targets' => ['get' => ['responses' => ['200' => [
                    'description' => 'A target',
                    'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/RefTarget']]],
                ]]]]],
                'components' => ['schemas' => ['RefTarget' => ['type' => 'string', 'description' => 'Written']]],
            ])
            ->build();

        $this->assertSame('A target for refs.', $result->toArray()['components']['schemas']['RefTarget']['description']);
        $this->assertCount(1, array_filter(array_column($result->log(), 'message'), fn (string $message): bool => str_contains($message, 'RefTarget')));
    }

    public function testADocumentOfAnotherVersionIsReadAs31WithAWarning(): void
    {
        $base = (string) tempnam(sys_get_temp_dir(), 'openapi');
        $file = $base . '.yaml';
        file_put_contents($file, "openapi: 3.0.3\ninfo:\n  title: Written\n  version: 1.0.0\npaths:\n  /a:\n    get:\n      responses:\n        '200':\n          description: ok\n");

        try {
            $result = (new Builder())->setMode(Mode::SPEC)->withDocument($file)->build();
        } finally {
            unlink($file);
            unlink($base);
        }

        $this->assertSame('Written', $result->toArray()['info']['title']);
        $this->assertSame(['No parser supports OpenAPI 3.0.3, read with OpenApi\Compiler\OpenApi31Parser'], array_column($result->log(), 'message'));
    }

    public function testClassicModeDoesNotReadADocument(): void
    {
        $result = (new Builder())
            ->setMode(Mode::CLASSIC)
            ->withDocument(['openapi' => '3.1.0', 'info' => ['title' => 'Written', 'version' => '1.0.0']])
            ->build();

        $this->assertContains('Documents are added to the spec pipeline and are not read in classic mode, ignored', array_column($result->log(), 'message'));
    }
}
