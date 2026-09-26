<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Snippets\Guide\WhySpec as Snippet;
use OpenApi\Tests\Concerns\AssertsSpecEquals;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * Runs the snippet `guide/why-spec.md` transcludes. `DocSnippetsTest` cannot: it builds every
 * snippet by scanning a source, and this one has no source to scan — that is its subject.
 */
final class WhySpecSnippetTest extends TestCase
{
    use AssertsSpecEquals;

    private const SNIPPETS = __DIR__ . '/../docs/snippets/guide/why-spec';

    public static function setUpBeforeClass(): void
    {
        require_once self::SNIPPETS . '/value_objects.php';
    }

    public function testValueObjectsProduceTheDocumentedOutput(): void
    {
        $this->assertSpecEquals(
            file_get_contents(self::SNIPPETS . '/value_objects-3.1.0.yaml'),
            Yaml::dump(Snippet\buildFromFields(['rate' => 'number', 'name' => 'string']), 10, 2)
        );
    }
}
