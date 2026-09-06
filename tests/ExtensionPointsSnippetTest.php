<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Augmenter\Cleanup;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Snippets\Guide\ExtensionPoints as Snippet;
use OpenApi\Tests\Concerns\AssertsSpecEquals;
use OpenApi\Utils\Pipeline;
use PHPUnit\Framework\TestCase;

/**
 * Runs the snippets `guide/extension-points.md` transcludes. `DocSnippetsTest` cannot: it
 * builds every snippet with one fixed `Builder`, so it has nowhere to register a translator.
 *
 * The test calls `buildSpec()` from `wiring.php` — the composed example the page shows —
 * rather than restating it, so the two cannot drift apart.
 */
final class ExtensionPointsSnippetTest extends TestCase
{
    use AssertsSpecEquals;

    private const SNIPPETS = __DIR__ . '/../docs/snippets/guide/extension-points';

    public static function setUpBeforeClass(): void
    {
        foreach (glob(self::SNIPPETS . '/*.php') as $snippet) {
            require_once $snippet;
        }
    }

    public function testWiringProducesTheDocumentedOutput(): void
    {
        $this->assertSpecEquals(
            file_get_contents(self::SNIPPETS . '/wiring-3.1.0.yaml'),
            Snippet\buildSpec()->toYaml()
        );
    }

    public function testSubclassedAttributeDerivesSchemaAndRequired(): void
    {
        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource(new \ReflectionClass(Snippet\Pet::class))
            ->setVersion('3.1.0')
            ->withAugmenters(fn (Pipeline $augmenters): Pipeline => $augmenters->remove(Cleanup::class))
            ->build();

        $schema = $result->specification()->schemas[0];

        $this->assertSame('Pet', $schema->schema);
        $this->assertSame(['name', 'age'], $schema->required);
    }
}
