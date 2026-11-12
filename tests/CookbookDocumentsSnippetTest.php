<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Snippets\Guide\Cookbook\Documents as Snippet;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * Runs the snippets the cookbook's "Add a hand-written document" transcludes.
 * `DocSnippetsTest` cannot: they configure their own `Builder` and read a YAML file.
 */
final class CookbookDocumentsSnippetTest extends TestCase
{
    private const SNIPPETS = __DIR__ . '/../docs/snippets/guide/cookbook/documents';

    public static function setUpBeforeClass(): void
    {
        foreach (glob(self::SNIPPETS . '/*.php') ?: [] as $snippet) {
            require_once $snippet;
        }
    }

    public function testTheDocumentAndTheAttributesMatchTheDocumentedOutput(): void
    {
        $result = Snippet\build()->build();

        $this->assertTrue($result->isValid());
        $this->assertEquals(Yaml::parseFile(self::SNIPPETS . '/documents-3.1.0.yaml'), $result->toArray());
    }
}
