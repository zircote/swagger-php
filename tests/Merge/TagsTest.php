<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Merge;

use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Merge;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Concerns\ExpectsLogEntries;
use OpenApi\Utils\TypedList;
use PHPUnit\Framework\TestCase;

final class TagsTest extends TestCase
{
    use ExpectsLogEntries;

    public function testIsADefaultMergerAheadOfTheCatchAll(): void
    {
        $mergers = array_map(static fn (object $merger): string => $merger::class, iterator_to_array((new Builder())->getMergers(), false));

        $this->assertSame([Merge\Tags::class, Merge\LastWins::class], $mergers);
    }

    public function testANameOnlyTagAddsNothingAndIsNotReported(): void
    {
        $tags = $this->build(
            new OA\Tag(name: 'pets', description: 'Everything about pets', x: ['order' => 1]),
            new OA\Tag(name: 'pets'),
        );

        $this->assertSame([['name' => 'pets', 'description' => 'Everything about pets', 'x-order' => 1]], $tags);
    }

    public function testEachKeepsWhatTheOtherLeavesUnset(): void
    {
        $tags = $this->build(
            new OA\Tag(name: 'pets', description: 'Everything about pets'),
            new OA\Tag(name: 'pets', externalDocs: new OA\ExternalDocumentation(url: 'https://example.com/pets')),
        );

        $this->assertSame([['name' => 'pets', 'description' => 'Everything about pets', 'externalDocs' => ['url' => 'https://example.com/pets']]], $tags);
    }

    public function testModeFirstKeepsTheEarlierValue(): void
    {
        $this->expectLogEntry('Tag "pets" is declared more than once, folding into the first in unknown; description set differently', 'warning');

        $tags = $this->build(
            new OA\Tag(name: 'pets', description: 'Everything about pets'),
            new OA\Tag(name: 'pets', description: 'Pets'),
            Merge\Mode::First,
        );

        $this->assertSame([['name' => 'pets', 'description' => 'Everything about pets']], $tags);
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function build(OA\Tag $earlier, OA\Tag $later, ?Merge\Mode $mode = null): array
    {
        $builder = (new Builder())
            ->setMode(Mode::SPEC)
            ->setLogger($this->trackingLogger())
            ->withSpecification(function (Specification $specification) use ($earlier, $later): void {
                $specification->add(
                    new OA\Info(title: 'T', version: '1.0'),
                    new OA\Operation\Get(path: '/pets', tags: ['pets'], responses: [new OA\Response(response: 200, description: 'OK')]),
                    $earlier,
                    $later,
                );
            });

        if ($mode instanceof Merge\Mode) {
            $builder->withMergers(fn (TypedList $mergers): TypedList => $mergers->remove(Merge\Tags::class)->insert(new Merge\Tags($mode), Merge\LastWins::class));
        }

        return $builder->build()->toArray()['tags'];
    }
}
