<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Utils;

use OpenApi\Utils\PipeInterface;
use OpenApi\Utils\Pipeline;
use PHPUnit\Framework\TestCase;

final class PipelineTest extends TestCase
{
    protected function pipe(string $add): callable
    {
        return fn (string $payload): string => $payload . $add;
    }

    protected function namedPipe(string $name): object
    {
        return new class ($name) {
            public function __construct(public readonly string $name)
            {
            }

            public function __invoke(mixed $payload): mixed
            {
                return null;
            }
        };
    }

    protected function groupedPipe(string $group, array &$log): PipeInterface
    {
        return new class ($group, $log) implements PipeInterface {
            public function __construct(
                protected string $group,
                protected array &$log,
            ) {
            }

            public function group(): string
            {
                return $this->group;
            }

            public function __invoke(mixed $payload): mixed
            {
                $this->log[] = $this->group;

                return null;
            }
        };
    }

    public function testProcess(): void
    {
        $pipeline = new Pipeline([$this->pipe('x')]);

        $this->assertSame('x', $pipeline->process(''));
    }

    public function testAdd(): void
    {
        $pipeline = new Pipeline();
        $pipeline->add($this->pipe('a'));
        $pipeline->add($this->pipe('b'));

        $this->assertSame('ab', $pipeline->process(''));
    }

    public function testRemoveByInstance(): void
    {
        $a = $this->pipe('a');
        $b = $this->pipe('b');
        $pipeline = new Pipeline([$a, $b]);

        $pipeline->remove($a);

        $this->assertSame('b', $pipeline->process(''));
    }

    public function testRemoveByMatcher(): void
    {
        $keep = $this->namedPipe('keep');
        $remove = $this->namedPipe('remove');
        $pipeline = new Pipeline([$keep, $remove]);

        $pipeline->remove(null, fn ($pipe): bool => $pipe->name !== 'remove');

        $found = [];
        $pipeline->walk(function ($pipe) use (&$found): void {
            $found[] = $pipe->name;
        });
        $this->assertSame(['keep'], $found);
    }

    public function testRemoveByClassString(): void
    {
        $a = $this->pipe('a');
        $b = new PipelineTestMarkerPipe('b');
        $pipeline = new Pipeline([$a, $b]);

        $pipeline->remove(PipelineTestMarkerPipe::class);

        $this->assertSame('a', $pipeline->process(''));
    }

    public function testInsertByClassString(): void
    {
        $a = $this->pipe('a');
        $b = new PipelineTestMarkerPipe('b');
        $pipeline = new Pipeline([$a, $b]);

        $c = $this->pipe('c');
        $pipeline->insert($c, PipelineTestMarkerPipe::class);

        $this->assertSame('acb', $pipeline->process(''));
    }

    public function testInsertByMatcher(): void
    {
        $pipeline = new Pipeline([$this->pipe('a'), $this->pipe('c')]);

        $pipeline->insert($this->pipe('b'), fn ($pipes): int => 1);

        $this->assertSame('abc', $pipeline->process(''));
    }

    public function testWalk(): void
    {
        $a = $this->namedPipe('a');
        $b = $this->namedPipe('b');
        $pipeline = new Pipeline([$a, $b]);

        $names = [];
        $pipeline->walk(function ($pipe) use (&$names): void {
            $names[] = $pipe->name;
        });

        $this->assertSame(['a', 'b'], $names);
    }

    // --- Grouping ---

    public function testGroupOrderOverridesInsertionOrder(): void
    {
        $log = [];

        $resolve = $this->groupedPipe('resolve', $log);
        $augment = $this->groupedPipe('augment', $log);

        $pipeline = new Pipeline(
            [$augment, $resolve],
            groups: ['resolve', 'reduce', 'augment'],
            defaultGroup: 'augment',
        );

        $pipeline->process('ignored');

        $this->assertSame(['resolve', 'augment'], $log);
    }

    public function testDefaultGroupForPlainCallables(): void
    {
        $log = [];

        $pipeline = new Pipeline(
            [function ($p) use (&$log): void {
                $log[] = 'plain';
            }],
            groups: ['first', 'default'],
            defaultGroup: 'default',
        );

        $pipeline->process('x');

        $this->assertSame(['plain'], $log);
    }

    public function testEnumGroupsWork(): void
    {
        $log = [];

        $pipeline = new Pipeline(
            [$this->groupedPipe('resolve', $log), $this->groupedPipe('augment', $log)],
            groups: ['resolve', 'reduce', 'augment'],
            defaultGroup: 'augment',
        );

        $result = $pipeline->process('payload');

        $this->assertSame('payload', $result);
        $this->assertSame(['resolve', 'augment'], $log);
    }

    // --- get() ---

    public function testGetReturnsMatchingPipe(): void
    {
        $a = $this->namedPipe('a');
        $pipeline = new Pipeline([$a]);

        $this->assertSame($a, $pipeline->get($a::class));
    }

    public function testGetReturnsNullForMissing(): void
    {
        $pipeline = new Pipeline([$this->pipe('x')]);

        $this->assertNotInstanceOf(\stdClass::class, $pipeline->get(\stdClass::class));
    }

    // --- No groups (BC) ---

    public function testNoGroupsUsesInsertionOrder(): void
    {
        $pipeline = new Pipeline([$this->pipe('a'), $this->pipe('b'), $this->pipe('c')]);

        $this->assertSame('abc', $pipeline->process(''));
    }
}

class PipelineTestMarkerPipe
{
    public function __construct(private readonly string $add)
    {
    }

    public function __invoke(string $payload): string
    {
        return $payload . $this->add;
    }
}
