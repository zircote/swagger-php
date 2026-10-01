<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\MergerInterface;
use OpenApi\Merge;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\TypedList;
use PHPUnit\Framework\TestCase;

/**
 * One collision per root collection, contributed so both halves are identical in source and the
 * report is the only difference between them. A contribution is the cheapest way to produce a
 * duplicate on purpose.
 *
 * A collision inside one attribute is not this pass's: `CompilerTest` covers those, where they
 * are reported and the compiler's own last-write-wins stands.
 */
final class MergeTest extends TestCase
{
    public function testDuplicateOperationsKeepTheLast(): void
    {
        $result = $this->build(function (Specification $specification): void {
            $specification->add(
                new OA\Operation\Get(path: '/pets', summary: 'FIRST', responses: [new OA\Response(response: 200, description: 'ok')]),
                new OA\Operation\Get(path: '/pets', summary: 'SECOND', responses: [new OA\Response(response: 200, description: 'ok')]),
            );
        });

        $this->assertSame('SECOND', $result->toArray()['paths']['/pets']['get']['summary']);
        $this->assertCount(1, $result->specification()->operations);
        $this->assertMatchesWarning('Get "get /pets" is declared more than once', $result);
    }

    /**
     * Without the pass, the `+` union in `compilePaths()` keeps the *first* path item for a path
     * while the *last* operation wins.
     */
    public function testDuplicatePathItemsNowKeepTheLast(): void
    {
        $result = $this->build(function (Specification $specification): void {
            $specification->add(new OA\Operation\Get(path: '/pets', responses: [new OA\Response(response: 200, description: 'ok')]));

            foreach (['FIRST', 'SECOND'] as $summary) {
                $pathItem = new OA\PathItem(summary: $summary);
                $pathItem->path = '/pets';
                $specification->add($pathItem);
            }
        });

        $path = $result->toArray()['paths']['/pets'];

        $this->assertSame('SECOND', $path['summary']);
        $this->assertArrayHasKey('get', $path, 'the path item is folded into the operations already written for the path');
        $this->assertMatchesWarning('PathItem "/pets" is declared more than once', $result);
    }

    public function testDuplicateWebhookOperationsAreReported(): void
    {
        $result = $this->build(function (Specification $specification): void {
            $specification->add(
                new OA\Operation\Post(webhook: 'petCreated', summary: 'FIRST', responses: [new OA\Response(response: 200, description: 'ok')]),
                new OA\Operation\Post(webhook: 'petCreated', summary: 'SECOND', responses: [new OA\Response(response: 200, description: 'ok')]),
            );
        });

        $this->assertSame('SECOND', $result->toArray()['webhooks']['petCreated']['post']['summary']);
        $this->assertMatchesWarning('Post "post petCreated" is declared more than once', $result);
    }

    public function testDuplicateTagsKeepTheLast(): void
    {
        $result = $this->build(function (Specification $specification): void {
            $specification->add(
                new OA\Tag(name: 'pets', description: 'FIRST'),
                new OA\Tag(name: 'pets', description: 'SECOND'),
                new OA\Operation\Get(path: '/pets', tags: ['pets'], responses: [new OA\Response(response: 200, description: 'ok')]),
            );
        });

        $tags = $result->toArray()['tags'];

        $this->assertCount(1, $tags, 'the specification requires each tag name in the list to be unique');
        $this->assertSame('SECOND', $tags[0]['description']);
        $this->assertMatchesWarning('Tag "pets" is declared more than once', $result);
    }

    public function testDuplicateComponentsKeepTheLast(): void
    {
        $result = $this->build(function (Specification $specification): void {
            $specification->add(
                new OA\Operation\Get(path: '/pets', responses: [
                    new OA\Response(response: 200, description: 'ok', content: [
                        new OA\MediaType\Json(schema: new OA\Schema(ref: '#/components/schemas/Pet')),
                    ]),
                ]),
                new OA\Schema(description: 'FIRST', component: 'Pet'),
                new OA\Schema(description: 'SECOND', component: 'Pet'),
            );
        });

        $schemas = $result->toArray()['components']['schemas'];

        $this->assertCount(1, $schemas);
        $this->assertSame('SECOND', $schemas['Pet']['description']);
        $this->assertMatchesWarning('Schema "Pet" is declared more than once', $result);
    }

    /**
     * Servers and security requirements are positional: their entries have no identity, two
     * identical ones are legal, and nothing is reduced or reported.
     */
    public function testPositionalListsAreLeftAlone(): void
    {
        $result = $this->build(function (Specification $specification): void {
            $specification->add(
                new OA\Server(url: 'https://example.com'),
                new OA\Server(url: 'https://example.com'),
                new OA\Operation\Get(path: '/pets', responses: [new OA\Response(response: 200, description: 'ok')]),
            );
        });

        $this->assertCount(2, $result->toArray()['servers']);
        $this->assertSame([], $result->warnings());
    }

    /**
     * The policy a contributing package registers: what the scan described keeps the key, and
     * the package recognises its own half by what it put in `meta`.
     */
    public function testAMergerCanRecogniseWhatItsProducerMarked(): void
    {
        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->withMergers(fn (TypedList $mergers): TypedList => $mergers->insert(new ContributionsYield(), Merge\LastWins::class))
            ->withSpecification(function (Specification $specification): void {
                $specification->add(
                    new OA\Info(title: 'T', version: '1.0'),
                    new OA\Operation\Get(path: '/pets', summary: 'DESCRIBED', responses: [new OA\Response(response: 200, description: 'ok')]),
                    (new OA\Operation\Get(path: '/pets', summary: 'CONTRIBUTED', responses: [new OA\Response(response: 200, description: 'ok')]))
                        ->setMeta(ContributionsYield::class, true),
                );
            })
            ->build();

        $this->assertSame('DESCRIBED', $result->toArray()['paths']['/pets']['get']['summary']);
        $this->assertSame([], $result->warnings());
    }

    public function testTheMergerThatClaimsTheTypeFirstDecides(): void
    {
        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->withMergers(fn (TypedList $mergers): TypedList => $mergers->insert(new FirstWinsForOperations(), Merge\LastWins::class))
            ->withSpecification(function (Specification $specification): void {
                $specification->add(
                    new OA\Info(title: 'T', version: '1.0'),
                    new OA\Operation\Get(path: '/pets', summary: 'FIRST', tags: ['pets'], responses: [new OA\Response(response: 200, description: 'ok')]),
                    new OA\Operation\Get(path: '/pets', summary: 'SECOND', tags: ['pets'], responses: [new OA\Response(response: 200, description: 'ok')]),
                    new OA\Tag(name: 'pets', description: 'FIRST'),
                    new OA\Tag(name: 'pets', description: 'SECOND'),
                );
            })
            ->build();

        $this->assertSame('FIRST', $result->toArray()['paths']['/pets']['get']['summary'], 'the registered merger claims operations');
        $this->assertSame('SECOND', $result->toArray()['tags'][0]['description'], 'everything else still falls to the catch-all');
        $this->assertCount(1, $result->warnings(), 'a merger that folds silently reports nothing');
    }

    /**
     * The pass is registered twice, and the second run is the last of the default pipes, so
     * what an augmenter adds is reduced too.
     */
    public function testTheLastRunReducesWhatAnAugmenterAdded(): void
    {
        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->withAugmenters(fn ($pipeline) => $pipeline->insert(
                function (Specification $specification): void {
                    $specification->add(new OA\Operation\Get(path: '/pets', summary: 'LATE', responses: [new OA\Response(response: 200, description: 'ok')]));
                },
                Augmenter\Merge::class,
            ))
            ->withSpecification(function (Specification $specification): void {
                $specification->add(
                    new OA\Info(title: 'T', version: '1.0'),
                    new OA\Operation\Get(path: '/pets', summary: 'FIRST', responses: [new OA\Response(response: 200, description: 'ok')]),
                );
            })
            ->build();

        $this->assertSame('LATE', $result->toArray()['paths']['/pets']['get']['summary']);
        $this->assertMatchesWarning('Get "get /pets" is declared more than once', $result);
    }

    public function testRemovingThePassRestoresTheUndiagnosedLastWins(): void
    {
        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->withAugmenters(fn ($pipeline) => $pipeline->remove(Augmenter\Merge::class))
            ->withSpecification(function (Specification $specification): void {
                $specification->add(
                    new OA\Info(title: 'T', version: '1.0'),
                    new OA\Operation\Get(path: '/pets', summary: 'FIRST', responses: [new OA\Response(response: 200, description: 'ok')]),
                    new OA\Operation\Get(path: '/pets', summary: 'SECOND', responses: [new OA\Response(response: 200, description: 'ok')]),
                );
            })
            ->build();

        $this->assertCount(2, $result->specification()->operations, 'both runs go, so nothing is reduced');
        $this->assertSame('SECOND', $result->toArray()['paths']['/pets']['get']['summary']);
        $this->assertStringStartsWith(
            'operationId must be unique',
            $result->warnings()[0],
            'the only signal left is the accidental one, and it guards a different key',
        );
        $this->assertCount(1, $result->warnings());
    }

    protected function build(callable $contribution): Builder\Result
    {
        return (new Builder())
            ->setMode(Mode::SPEC)
            ->withSpecification(function (Specification $specification) use ($contribution): void {
                $specification->add(new OA\Info(title: 'T', version: '1.0'));
                $contribution($specification);
            })
            ->build();
    }

    protected function assertMatchesWarning(string $expected, Builder\Result $result): void
    {
        $matching = array_filter($result->warnings(), fn (string $warning): bool => str_contains($warning, $expected));

        $this->assertCount(1, $matching, "expected exactly one warning containing '{$expected}', got: " . implode(' | ', $result->warnings()));
    }
}

/**
 * A consumer's precedence policy: an operation already described stays as it is, and folding is
 * not worth reporting. `supports()` claims one type, so everything else falls through to the
 * catch-all registered behind it.
 */
final class FirstWinsForOperations implements MergerInterface
{
    public function supports(string $class): bool
    {
        return is_a($class, OA\Operation::class, true);
    }

    public function identity(AttributeInterface $attribute): ?string
    {
        return $attribute instanceof OA\Operation && $attribute->path !== null && $attribute->method !== null
            ? $attribute->method . ' ' . $attribute->path
            : null;
    }

    public function merge(AttributeInterface $earlier, AttributeInterface $later): AttributeInterface
    {
        return $earlier;
    }
}

/**
 * An operation marked as contributed gives way to one that is not.
 */
final class ContributionsYield implements MergerInterface
{
    public function supports(string $class): bool
    {
        return is_a($class, OA\Operation::class, true);
    }

    public function identity(AttributeInterface $attribute): ?string
    {
        return $attribute instanceof OA\Operation && $attribute->path !== null && $attribute->method !== null
            ? $attribute->method . ' ' . $attribute->path
            : null;
    }

    public function merge(AttributeInterface $earlier, AttributeInterface $later): AttributeInterface
    {
        return $later->getMeta(self::class, false) ? $earlier : $later;
    }
}
