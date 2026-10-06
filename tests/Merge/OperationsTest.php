<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Merge;

use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Contracts\AttributeInterface;
use OpenApi\Merge;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Undefined;
use OpenApi\Utils\TypedList;
use PHPUnit\Framework\TestCase;

/**
 * Two halves of one operation, contributed in producer order: what a route table gives first,
 * then an attribute adding to it. The halves differ field by field, so each test reads one
 * rule off the document.
 */
final class OperationsTest extends TestCase
{
    public function testIsNotADefaultMerger(): void
    {
        $mergers = array_map(static fn (object $merger): string => $merger::class, iterator_to_array((new Builder())->getMergers(), false));

        $this->assertSame([Merge\LastWins::class], $mergers);
    }

    /**
     * `TypedList` finds a merger by `instanceof`, so the fold must not answer to the catch-all's
     * name: configuring `LastWins` by class leaves it where it is.
     */
    public function testTheCatchAllsNameDoesNotReachTheFold(): void
    {
        $operations = new Merge\Operations();
        $mergers = (new Builder())->getMergers()->insert($operations, Merge\LastWins::class);

        $this->assertInstanceOf(Merge\LastWins::class, $mergers->get(Merge\LastWins::class));
        $this->assertNotSame($operations, $mergers->get(Merge\LastWins::class));

        $marker = new Merge\LastWins();
        $mergers->insert($marker, Merge\LastWins::class);
        $this->assertSame([$operations, $marker], array_slice(iterator_to_array($mergers, false), 0, 2), 'inserted ahead of the catch-all, behind the fold');

        $mergers->remove(Merge\LastWins::class);
        $this->assertSame([$operations], iterator_to_array($mergers, false));
    }

    public function testEachHalfKeepsWhatTheOtherLeavesUnset(): void
    {
        $result = $this->build(
            new OA\Operation\Get(
                path: '/users/{id}',
                summary: 'Show one user',
                parameters: [new OA\Parameter\Path(name: 'id', schema: new OA\Schema(type: 'string', pattern: '[0-9]+'))],
            ),
            new OA\Operation\Get(
                path: '/users/{id}',
                responses: [new OA\Response(response: 200, description: 'The user')],
            ),
        );

        $operation = $result->toArray()['paths']['/users/{id}']['get'];
        $this->assertSame('Show one user', $operation['summary']);
        $this->assertSame('[0-9]+', $operation['parameters'][0]['schema']['pattern']);
        $this->assertSame('The user', $operation['responses'][200]['description']);
        $this->assertSame([], $result->warnings(), 'filling a gap is the point of the fold, not a collision');
    }

    public function testAParameterBothDescribeFoldsSchemaFieldByField(): void
    {
        $result = $this->build(
            new OA\Operation\Get(
                path: '/users/{id}',
                parameters: [new OA\Parameter\Path(name: 'id', schema: new OA\Schema(type: 'string', pattern: '[0-9]+'))],
                responses: [new OA\Response(response: 200, description: 'ok')],
            ),
            new OA\Operation\Get(
                path: '/users/{id}',
                parameters: [new OA\Parameter\Path(name: 'id', description: 'The user id', schema: new OA\Schema(type: 'integer'))],
            ),
        );

        $parameters = $result->toArray()['paths']['/users/{id}']['get']['parameters'];
        $this->assertCount(1, $parameters, 'one parameter, keyed by name and location');
        $this->assertSame('The user id', $parameters[0]['description']);
        $this->assertSame('integer', $parameters[0]['schema']['type'], 'the later half wins a field both set');
        $this->assertSame('[0-9]+', $parameters[0]['schema']['pattern'], 'and keeps one only the earlier set');
        $this->assertMatchesWarning('folding into the last in unknown; parameter path:id.schema.type set differently', $result);
    }

    public function testParametersInDifferentLocationsStayApart(): void
    {
        $result = $this->build(
            new OA\Operation\Get(
                path: '/users/{id}',
                parameters: [new OA\Parameter\Path(name: 'id', schema: new OA\Schema(type: 'string'))],
                responses: [new OA\Response(response: 200, description: 'ok')],
            ),
            new OA\Operation\Get(
                path: '/users/{id}',
                parameters: [new OA\Parameter\Query(name: 'id', schema: new OA\Schema(type: 'string'))],
            ),
        );

        $parameters = $result->toArray()['paths']['/users/{id}']['get']['parameters'];
        $this->assertSame(['path', 'query'], array_column($parameters, 'in'));
    }

    public function testAResponseBothDescribeFolds(): void
    {
        $result = $this->build(
            new OA\Operation\Get(path: '/users', responses: [new OA\Response(response: 200, description: 'FIRST')]),
            new OA\Operation\Get(path: '/users', responses: [
                new OA\Response(response: 200, description: 'SECOND'),
                new OA\Response(response: 404, description: 'Not found'),
            ]),
        );

        $responses = $result->toArray()['paths']['/users']['get']['responses'];
        $this->assertSame('SECOND', $responses[200]['description']);
        $this->assertSame('Not found', $responses[404]['description']);
        $this->assertMatchesWarning('response 200.description', $result);
    }

    public function testAConflictKeepsTheLaterAndNamesTheField(): void
    {
        $result = $this->build(
            new OA\Operation\Get(path: '/users', summary: 'FIRST', responses: [new OA\Response(response: 200, description: 'ok')]),
            new OA\Operation\Get(path: '/users', summary: 'SECOND'),
        );

        $this->assertSame('SECOND', $result->toArray()['paths']['/users']['get']['summary']);
        $this->assertMatchesWarning('Get "get /users" is declared more than once', $result);
        $this->assertMatchesWarning('folding into the last in unknown; summary set differently', $result);
    }

    public function testAnExplicitEmptySecurityFromTheLaterHalfOptsOut(): void
    {
        $result = $this->build(
            new OA\Operation\Get(path: '/status', responses: [new OA\Response(response: 200, description: 'ok')], security: [new OA\Security\Requirement(scheme: 'apiKey')]),
            new OA\Operation\Get(path: '/status', security: []),
        );

        $this->assertSame([], $result->toArray()['paths']['/status']['get']['security']);
    }

    public function testTagsAreAUnionAndExtensionsMerge(): void
    {
        $result = $this->build(
            new OA\Operation\Get(path: '/users', tags: ['users'], responses: [new OA\Response(response: 200, description: 'ok')], x: ['first' => 1, 'both' => 'first']),
            new OA\Operation\Get(path: '/users', tags: ['users', 'admin'], x: ['both' => 'second']),
        );

        $operation = $result->toArray()['paths']['/users']['get'];
        $this->assertSame(['users', 'admin'], $operation['tags']);
        $this->assertSame(1, $operation['x-first']);
        $this->assertSame('second', $operation['x-both']);
    }

    public function testFirstKeepsTheEarlierValueOfAConflict(): void
    {
        $result = $this->build(
            new OA\Operation\Get(
                path: '/users/{id}',
                summary: 'FIRST',
                parameters: [new OA\Parameter\Path(name: 'id', schema: new OA\Schema(type: 'string', pattern: '[0-9]+'))],
                responses: [new OA\Response(response: 200, description: 'ok')],
            ),
            new OA\Operation\Get(
                path: '/users/{id}',
                summary: 'SECOND',
                parameters: [new OA\Parameter\Path(name: 'id', description: 'The user id', schema: new OA\Schema(type: 'integer'))],
            ),
            mode: Merge\Mode::First,
        );

        $operation = $result->toArray()['paths']['/users/{id}']['get'];
        $this->assertSame('FIRST', $operation['summary']);
        $this->assertSame('string', $operation['parameters'][0]['schema']['type']);
        $this->assertSame('The user id', $operation['parameters'][0]['description'], 'a gap in a shared parameter is still filled');
        $this->assertMatchesWarning('folding into the first in unknown; summary, parameter path:id.schema.type set differently', $result);
    }

    public function testNonOverlappingTakesOnlyWhatOneHalfHasAlone(): void
    {
        $result = $this->build(
            new OA\Operation\Get(
                path: '/users/{id}',
                summary: 'FIRST',
                parameters: [new OA\Parameter\Path(name: 'id', schema: new OA\Schema(type: 'string', pattern: '[0-9]+'))],
            ),
            new OA\Operation\Get(
                path: '/users/{id}',
                summary: 'SECOND',
                description: 'Only the later half has one',
                parameters: [
                    new OA\Parameter\Path(name: 'id', description: 'The user id', schema: new OA\Schema(type: 'integer')),
                    new OA\Parameter\Query(name: 'expand', schema: new OA\Schema(type: 'string')),
                ],
                responses: [new OA\Response(response: 200, description: 'ok')],
            ),
            mode: Merge\Mode::NonOverlapping,
        );

        $operation = $result->toArray()['paths']['/users/{id}']['get'];
        $this->assertSame('FIRST', $operation['summary']);
        $this->assertSame('Only the later half has one', $operation['description']);
        $this->assertSame(['id', 'expand'], array_column($operation['parameters'], 'name'));
        $this->assertArrayNotHasKey('description', $operation['parameters'][0], "a parameter both describe is the earlier half's, whole");
        $this->assertSame('string', $operation['parameters'][0]['schema']['type']);
        $this->assertMatchesWarning('taking only what does not overlap in unknown; summary, parameter path:id.schema.type set differently', $result);
    }

    public function testAnOverlapThatAgreesIsNotReported(): void
    {
        $result = $this->build(
            new OA\Operation\Get(path: '/users', summary: 'SAME', responses: [new OA\Response(response: 200, description: 'ok')]),
            new OA\Operation\Get(path: '/users', summary: 'SAME', responses: [new OA\Response(response: 200, description: 'ok')]),
            mode: Merge\Mode::NonOverlapping,
        );

        $this->assertSame([], $result->warnings());
    }

    public function testACallableChoosesTheModePerPair(): void
    {
        // the later half is a contribution its producer marked, and the earlier half wins over it
        $route = new OA\Operation\Get(path: '/users', summary: 'From the route');
        $route->setMeta('producer', 'routes');

        $result = $this->build(
            new OA\Operation\Get(path: '/users', summary: 'From the attribute', responses: [new OA\Response(response: 200, description: 'ok')]),
            $route,
            mode: static fn (AttributeInterface $earlier, AttributeInterface $later): Merge\Mode => $later->getMeta('producer') === 'routes' ? Merge\Mode::First : Merge\Mode::Last,
        );

        $this->assertSame('From the attribute', $result->toArray()['paths']['/users']['get']['summary']);
    }

    public function testACallableMustReturnAMode(): void
    {
        /* @phpstan-ignore argument.type (intentionally the wrong return type, to reach the runtime check) */
        $merger = new Merge\Operations(static fn (): string => 'last');

        $this->expectException(\UnexpectedValueException::class);
        $merger->merge(new OA\Operation\Get(path: '/users'), new OA\Operation\Get(path: '/users'));
    }

    public function testNeitherHalfIsChanged(): void
    {
        $earlier = new OA\Operation\Get(path: '/users', summary: 'FIRST');
        $later = new OA\Operation\Get(path: '/users', description: 'From the attribute');

        $survivor = (new Merge\Operations())->merge($earlier, $later);

        $this->assertNotSame($earlier, $survivor);
        $this->assertNotSame($later, $survivor);
        $this->assertTrue(Undefined::isDefault($earlier->description));
        $this->assertTrue(Undefined::isDefault($later->summary));
    }

    protected function build(OA\Operation $earlier, OA\Operation $later, Merge\Mode|callable $mode = Merge\Mode::Last): Builder\Result
    {
        $operations = [$earlier, $later];

        return (new Builder())
            ->setMode(Mode::SPEC)
            ->withMergers(fn (TypedList $mergers): TypedList => $mergers->insert(new Merge\Operations($mode), Merge\LastWins::class))
            ->withSpecification(function (Specification $specification) use ($operations): void {
                $specification->add(new OA\Info(title: 'T', version: '1.0'), ...$operations);
            })
            ->build();
    }

    protected function assertMatchesWarning(string $expected, Builder\Result $result): void
    {
        $matching = array_filter($result->warnings(), fn (string $warning): bool => str_contains($warning, $expected));

        $this->assertCount(1, $matching, "expected exactly one warning containing '{$expected}', got: " . implode(' | ', $result->warnings()));
    }
}
