<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Specification;

use OpenApi\Augmenter;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Specification\ComponentName;
use OpenApi\Tests\Concerns\AssemblesSpecification;
use OpenApi\Tests\Fixtures\Augmenter\TypeSchema;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * One field, `component`, is the key on every reusable type. The historic per-type spellings
 * alias onto it — in the constructor where the field can only mean the key, in `normalise()`
 * where it is also a nesting key — and each is reported as deprecated exactly once.
 */
final class ComponentNameTest extends TestCase
{
    use AssemblesSpecification;

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

    /**
     * @return iterable<string, array{callable(): OA\AbstractAttribute, string}>
     */
    public static function constructorAliases(): iterable
    {
        yield 'schema' => [static fn (): OA\AbstractAttribute => new OA\Schema(schema: 'Pet'), 'schema'];
        yield 'parameter' => [static fn (): OA\AbstractAttribute => new OA\Parameter(parameter: 'page', name: 'page'), 'parameter'];
        yield 'request' => [static fn (): OA\AbstractAttribute => new OA\RequestBody(request: 'Body'), 'request'];
        yield 'securityScheme' => [static fn (): OA\AbstractAttribute => new OA\Security\Scheme(securityScheme: 'api', type: 'apiKey'), 'securityScheme'];
    }

    #[DataProvider('constructorAliases')]
    public function testASpellingThatCanOnlyBeTheKeyAliasesInTheConstructor(callable $build, string $field): void
    {
        $attribute = $build();

        $this->assertSame($attribute->{$field}, $attribute->component);
        $this->assertCount(1, $this->deprecations);
        $this->assertStringContainsString("`{$field}` is deprecated as the component key", $this->deprecations[0]);
    }

    public function testComponentWinsOverTheSpellingAndIsNotReported(): void
    {
        $schema = new OA\Schema(schema: 'Old', component: 'New');

        $this->assertSame('New', $schema->component);
        $this->assertSame('Old', $schema->schema, 'the historic field is left as written');
        $this->assertSame([], $this->deprecations);
    }

    /**
     * @return iterable<string, array{OA\AbstractAttribute, string|null}>
     */
    public static function keysRead(): iterable
    {
        yield 'response by component' => [new OA\Response(response: 404, component: 'NotFound'), 'NotFound'];
        yield 'response by spelling' => [new OA\Response(response: 'NotFound'), 'NotFound'];
        yield 'response code as spelling' => [new OA\Response(response: 200), '200'];
        yield 'header by spelling' => [new OA\Header(header: 'RateLimit'), 'RateLimit'];
        yield 'link by spelling' => [new OA\Link(link: 'Self'), 'Self'];
        yield 'example by spelling' => [new OA\Example(example: 'Minimal'), 'Minimal'];
        yield 'parameter by name' => [new OA\Parameter(name: 'page'), 'page'];
        yield 'schema, title is not a key' => [new OA\Schema(title: 'A title'), null];
        yield 'path item by component' => [new OA\PathItem(component: 'Paged'), 'Paged'];
        yield 'path item, path-bound' => [new OA\PathItem(prefix: '/api'), null];
        yield 'media type by component' => [new OA\MediaType(mediaType: 'application/json-patch+json', component: 'Patch'), 'Patch'];
        yield 'media type inline' => [new OA\MediaType(mediaType: 'application/json'), null];
        yield 'not a component type' => [new OA\Tag(name: 'pets'), null];
    }

    #[DataProvider('keysRead')]
    public function testOfReadsComponentAndFallsBackToTheSpellingWhereItIsUnambiguous(OA\AbstractAttribute $attribute, ?string $expected): void
    {
        $this->assertSame($expected, ComponentName::of($attribute));
        $this->assertSame([], $this->deprecations, 'reading is never what reports the spelling');
    }

    public function testNormalizeWritesTheKeyAndReportsEachSpellingOnce(): void
    {
        $spec = new Specification();
        $spec->responses[] = $response = new OA\Response(response: 'NotFound');
        $spec->headers[] = $header = new OA\Header(header: 'RateLimit');
        $spec->links[] = $link = new OA\Link(link: 'Self');
        $spec->examples[] = $example = new OA\Example(example: 'Minimal');
        $spec->parameters[] = $parameter = new OA\Parameter(name: 'page');

        ComponentName::normalise($spec, deprecate: true);

        $this->assertSame('NotFound', $response->component);
        $this->assertSame('RateLimit', $header->component);
        $this->assertSame('Self', $link->component);
        $this->assertSame('Minimal', $example->component);
        $this->assertSame('page', $parameter->component, 'a parameter is keyed by its name');

        $fields = array_map(fn (string $m): string => explode('`', $m)[1], $this->deprecations);
        $this->assertSame(['response', 'header', 'link', 'example'], $fields, 'the inferences are not spellings and are not reported');

        $this->deprecations = [];
        ComponentName::normalise($spec, deprecate: true);
        $this->assertSame([], $this->deprecations, 'once written, there is nothing left to report');
    }

    public function testNormalizeDoesNotReportWhatItDidNotAuthor(): void
    {
        $spec = new Specification();
        $spec->responses[] = $response = new OA\Response(response: 'NotFound');

        ComponentName::normalise($spec, deprecate: false);

        $this->assertSame('NotFound', $response->component);
        $this->assertSame([], $this->deprecations);
    }

    public function testATitleNeverBecomesAKey(): void
    {
        $spec = $this->assemble(TypeSchema::class);
        $schema = $spec->schemas[0];
        $schema->component = null;
        $schema->title = 'A title, not a name';

        ComponentName::normalise($spec, deprecate: true);
        $this->assertNull($schema->component);

        (new Augmenter\Names())($spec);
        $this->assertSame('TypeSchema', $schema->component, 'the class names it, as before');
    }

    /**
     * @return iterable<string, array{OA\AbstractAttribute, bool}>
     */
    public static function rootness(): iterable
    {
        yield 'header, unkeyed' => [new OA\Header(description: 'inline'), false];
        yield 'header, keyed' => [new OA\Header(component: 'RateLimit'), true];
        yield 'header, nesting key only' => [new OA\Header(header: 'X-Rate-Limit'), false];
        yield 'example, keyed' => [new OA\Example(component: 'Minimal'), true];
        yield 'example, nesting key only' => [new OA\Example(example: 'minimal'), false];
        yield 'media type, keyed' => [new OA\MediaType(component: 'Patch'), true];
        yield 'media type, inline' => [new OA\MediaType(mediaType: 'application/json'), false];
        yield 'response, keyed' => [new OA\Response(component: 'NotFound'), true];
        yield 'response, keyed with ref' => [new OA\Response(ref: '#/components/responses/NotFound', component: 'Alias'), false];
        yield 'response, code with ref' => [new OA\Response(response: 404, ref: '#/components/responses/NotFound'), false];
        yield 'response, historic spelling' => [new OA\Response(response: 'NotFound'), true];
        yield 'link, historic spelling' => [new OA\Link(link: 'Self'), true];
        yield 'link, keyed with ref' => [new OA\Link(ref: '#/components/links/Other', component: 'Self'), false];
        yield 'request body, keyed' => [new OA\RequestBody(component: 'Body'), true];
        yield 'request body, keyed with ref' => [new OA\RequestBody(ref: '#/components/requestBodies/Body', component: 'Alias'), true];
        yield 'parameter, keyed' => [new OA\Parameter(name: 'page', component: 'page'), true];
        yield 'parameter, name only' => [new OA\Parameter(name: 'page'), false];
        yield 'path item, keyed' => [new OA\PathItem(component: 'Paged'), true];
        yield 'path item, path-bound' => [new OA\PathItem(prefix: '/api'), true];
    }

    #[DataProvider('rootness')]
    public function testIsRootFollowsComponent(OA\AbstractAttribute $attribute, bool $root): void
    {
        $this->assertSame($root, $attribute->isRoot());
    }
}
