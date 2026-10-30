<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Augmenter\Cleanup;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Resolver;
use OpenApi\Snippets\Guide\ExtensionPoints as Snippet;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Concerns\AssertsSpecEquals;
use OpenApi\Utils\Pipeline;
use OpenApi\Utils\TypedList;
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
        foreach (glob(self::SNIPPETS . '/*.php') ?: [] as $snippet) {
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

    public function testContributionProducesTheDocumentedOutput(): void
    {
        $this->assertSpecEquals(
            file_get_contents(self::SNIPPETS . '/contribution-3.1.0.yaml'),
            Snippet\buildFromRoutes(['/pets' => Snippet\Pet::class])->toYaml()
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

        $this->assertSame('Pet', $schema->component);
        $this->assertSame(['name', 'age'], $schema->required);
    }

    public function testDependencyProviderCollectsMetadataReferences(): void
    {
        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->withResolver(fn (Resolver $resolver): Resolver => $resolver->withDependencyProviders(
                fn (TypedList $providers): TypedList => $providers->add(new Snippet\MetadataDependencies()),
            ))
            ->withSpecification(function (Specification $specification): void {
                $operation = new OA\Operation\Get(path: '/pets', responses: [new OA\Response(response: 200, description: 'OK')]);
                $operation->setMeta('my-integration.dependencies', [Snippet\Pet::class]);
                $specification->add(new OA\Info(title: 'Metadata', version: '1.0'), $operation);
            })
            ->withAugmenters(fn (Pipeline $augmenters): Pipeline => $augmenters->remove(Cleanup::class))
            ->build();

        $this->assertTrue($result->isValid());
        $this->assertSame(['name', 'age'], $result->specification()->buildComponentIndex()->findSchema(Snippet\Pet::class)->required);
    }
}
