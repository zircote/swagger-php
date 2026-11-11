<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Compiler\OpenApi31Parser;
use OpenApi\Contracts\ParserInterface;
use OpenApi\Loggers\CollectingLogger;
use OpenApi\OpenApiException;
use OpenApi\Parser;
use OpenApi\Spec\Info;
use OpenApi\Specification;
use PHPUnit\Framework\TestCase;

final class ParserTest extends TestCase
{
    public function testADocumentIsReadByTheParserForItsVersion(): void
    {
        $logger = new CollectingLogger();
        $other = new class () implements ParserInterface {
            public function supports(string $version): bool
            {
                return str_starts_with($version, '3.0.');
            }

            public function parse(array $document): Specification
            {
                return new Specification();
            }
        };

        $parser = new Parser($logger, [$other, new OpenApi31Parser($logger)]);
        $specification = $parser->parse(['openapi' => '3.1.1', 'info' => ['title' => 'T', 'version' => '1']]);

        $this->assertSame('T', $specification->info?->title);
        $this->assertNull($parser->parse(['openapi' => '3.0.3', 'info' => ['title' => 'T', 'version' => '1']])->info);
        $this->assertSame([], $logger->entries());
    }

    public function testAnotherVersionIsWarnedAboutAndReadByTheLastParser(): void
    {
        $logger = new CollectingLogger();

        $specification = (new Parser($logger))->parse(['openapi' => '3.0.3', 'info' => ['title' => 'T', 'version' => '1']]);
        (new Parser($logger))->parse(['info' => ['title' => 'T', 'version' => '1']]);

        $this->assertSame([
            'No parser supports OpenAPI 3.0.3, read with OpenApi\Compiler\OpenApi31Parser',
            'The document has no OpenAPI version, read with OpenApi\Compiler\OpenApi31Parser',
        ], array_column($logger->entries(), 'message'));
        $this->assertSame('T', $specification->info?->title);
    }

    public function testAFileIsReadByItsExtension(): void
    {
        $specification = (new Parser())->parseFile(__DIR__ . '/../docs/examples/specs/api/api-3.1.0.yaml');

        $this->assertInstanceOf(Info::class, $specification->info);
    }

    public function testAFileThatDoesNotParseThrows(): void
    {
        $file = sys_get_temp_dir() . '/' . uniqid('parser', true) . '.json';
        file_put_contents($file, '{"openapi":');

        try {
            $this->expectException(OpenApiException::class);
            $this->expectExceptionMessage(sprintf('Cannot parse "%s"', $file));

            (new Parser())->parseFile($file);
        } finally {
            unlink($file);
        }
    }
}
