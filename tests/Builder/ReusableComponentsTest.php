<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Builder;

use OpenApi\Augmenter;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Concerns\UsesFixtures;
use OpenApi\Utils\Pipeline;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * `component:` end to end: the historic spellings produce the same document, a keyed header
 * stands alone, a keyed path item is a component and not a path, and a keyed media type is
 * one from 3.2 and reported before it.
 */
final class ReusableComponentsTest extends TestCase
{
    use UsesFixtures;

    /** @var list<string> */
    private array $deprecations = [];

    protected function setUp(): void
    {
        $this->deprecations = [];
        set_error_handler(function (int $errno, string $message): bool {
            if ($errno === E_USER_DEPRECATED) {
                $this->deprecations[] = $message;
            }

            return true;
        });
    }

    protected function tearDown(): void
    {
        restore_error_handler();
    }

    public function testBothSpellingsProduceOneDocument(): void
    {
        $legacy = $this->build(['LegacySpelling'])->toArray();
        $reported = $this->deprecations;

        $this->deprecations = [];
        $current = $this->build(['ComponentSpelling'])->toArray();

        $this->assertSame($current, $legacy, 'the alias is exact');
        $this->assertSame([], $this->deprecations, 'the current spelling reports nothing');

        sort($reported);
        $fields = array_values(array_unique(array_map(fn (string $m): string => explode('`', $m)[1], $reported)));
        $this->assertSame(['example', 'header', 'link', 'parameter', 'request', 'response', 'schema'], $fields, 'every historic spelling used is reported');

        foreach (['schemas' => 'Pet', 'responses' => 'NotFound', 'requestBodies' => 'PetBody', 'parameters' => 'page', 'headers' => 'RateLimit', 'links' => 'Self', 'examples' => 'Minimal'] as $bucket => $key) {
            $this->assertArrayHasKey($key, $current['components'][$bucket], $bucket);
        }
    }

    public function testTheNestingKeysAreNotReported(): void
    {
        $document = $this->build(['ComponentSpelling'])->toArray();
        $response = $document['paths']['/pets/{id}']['post']['responses'];

        $this->assertSame([], $this->deprecations);
        $this->assertSame('#/components/headers/RateLimit', $response[200]['headers']['X-Rate-Limit']['$ref'], '`header:` on a nested header is the header name');
        $this->assertSame('#/components/links/Self', $response[200]['links']['self']['$ref']);
        $this->assertSame('#/components/examples/Minimal', $response[200]['content']['application/json']['examples']['minimal']['$ref']);
        $this->assertSame('#/components/responses/NotFound', $response[404]['$ref'], '`response:` on a nested response is the status code');
    }

    public function testAKeyedHeaderStandsAlone(): void
    {
        $document = $this->build(['StandaloneHeader'])->toArray();

        $this->assertSame('Requests left', $document['components']['headers']['RateLimit']['description']);
        $this->assertSame('#/components/headers/RateLimit', $document['paths']['/things']['get']['responses'][200]['headers']['X-Rate-Limit']['$ref']);
    }

    public function testAKeyedPathItemIsAComponentAndGovernsNothing(): void
    {
        $result = $this->build(['SharedPathItem'], configure: fn (Builder $b): Builder => $b->withAugmenters(fn (Pipeline $p) => $p->get(Augmenter\Cleanup::class)?->setEnabled(false)));
        $document = $result->toArray();

        $this->assertSame(['/api/things'], array_keys($document['paths']), 'the prefixed path, and no path for the component');
        $this->assertSame('A paged listing', $document['components']['pathItems']['Paged']['summary']);
        $this->assertSame('page', $document['components']['pathItems']['Paged']['parameters'][0]['name']);
        $this->assertArrayNotHasKey('parameters', $document['paths']['/api/things']['get'], 'the component lends its parameters to no operation');
        $this->assertSame([], $result->warnings());
    }

    public function testAReferencedPathItemKeepsItsComponentThroughCleanup(): void
    {
        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->withSpecification(function (Specification $specification): void {
                $shared = new OA\PathItem(summary: 'A paged listing', component: 'Paged');
                $user = new OA\PathItem(ref: '#/components/pathItems/Paged');
                $user->path = '/users';
                $specification->add(new OA\Info(title: 'Ref', version: '1.0'), $shared, $user);
            })
            ->build();
        $document = $result->toArray();

        $this->assertSame('#/components/pathItems/Paged', $document['paths']['/users']['$ref']);
        $this->assertSame('A paged listing', $document['components']['pathItems']['Paged']['summary']);
    }

    public function testAPathItemComponentIsReportedAndOmittedIn30(): void
    {
        $result = $this->build(['SharedPathItem'], '3.0.0', fn (Builder $b): Builder => $b->withAugmenters(fn (Pipeline $p) => $p->get(Augmenter\Cleanup::class)?->setEnabled(false)));

        $this->assertArrayNotHasKey('components', $result->toArray());
        $this->assertContains('pathItems components are not supported in OpenAPI 3.0 and will be omitted', $result->warnings());
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function mediaTypeVersions(): iterable
    {
        yield '3.0.0' => ['3.0.0', false];
        yield '3.1.0' => ['3.1.0', false];
        yield '3.2.0' => ['3.2.0', true];
    }

    #[DataProvider('mediaTypeVersions')]
    public function testAKeyedMediaTypeIsAComponentFrom32(string $version, bool $supported): void
    {
        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->setVersion($version)
            ->withAugmenters(fn (Pipeline $p) => $p->get(Augmenter\Cleanup::class)?->setEnabled(false))
            ->withSpecification(function (Specification $specification): void {
                $specification->add(
                    new OA\Info(title: 'Patch', version: '1.0'),
                    new OA\Operation\Get(path: '/things', responses: [new OA\Response(response: 200, description: 'Things')]),
                    new OA\MediaType(mediaType: 'application/json-patch+json', schema: new OA\Schema(type: 'array'), component: 'Patch'),
                );
            })
            ->build();
        $document = $result->toArray();

        if ($supported) {
            $this->assertSame('array', $document['components']['mediaTypes']['Patch']['schema']['type']);
            $this->assertNotContains('mediaTypes components are not supported before OpenAPI 3.2 and will be omitted', $result->warnings());
        } else {
            $this->assertArrayNotHasKey('components', $document);
            $this->assertContains('mediaTypes components are not supported before OpenAPI 3.2 and will be omitted', $result->warnings());
        }
    }

    /**
     * The fixtures hold several classes per file — a component and the controller that
     * references it — so they are scanned as files.
     *
     * @param list<string> $files
     */
    private function build(array $files, string $version = '3.1.0', ?callable $configure = null): Builder\Result
    {
        $builder = (new Builder())->setMode(Mode::SPEC)->setVersion($version);
        foreach ($files as $file) {
            $path = self::fixture("ComponentKey/{$file}.php");
            require_once $path; // fixtures are excluded from the classmap, as ScratchTest's are
            $builder->addSource($path);
        }
        if ($configure !== null) {
            $configure($builder);
        }

        return $builder->build();
    }
}
