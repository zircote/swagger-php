<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Utils;

use OpenApi\Augmenter;
use OpenApi\Augmenter\Tag;
use OpenApi\PipeInterface;
use OpenApi\Specification;
use OpenApi\Utils\Pipeline;
use PHPUnit\Framework\TestCase;

final class PipelineTest extends TestCase
{
    protected function pipe(string $add): callable
    {
        return fn (string $payload): string => $payload . $add;
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
        $keep = new Augmenter\OperationId();
        $remove = new Tag();
        $pipeline = new Pipeline([$keep, $remove]);

        $pipeline->remove(null, fn ($pipe): bool => !$pipe instanceof Tag);

        $found = [];
        $pipeline->walk(function ($pipe) use (&$found): void {
            $found[] = $pipe::class;
        });
        $this->assertSame([Augmenter\OperationId::class], $found);
    }

    public function testRemoveByClassString(): void
    {
        $pipeline = new Pipeline([new Augmenter\OperationId(), new Tag()]);

        $pipeline->remove(Tag::class);

        $found = [];
        $pipeline->walk(function ($pipe) use (&$found): void {
            $found[] = $pipe::class;
        });
        $this->assertSame([Augmenter\OperationId::class], $found);
    }

    public function testInsertByClassString(): void
    {
        $pipeline = new Pipeline([new Augmenter\OperationId(), new Tag()]);

        $docblock = new Augmenter\Docblock();
        $pipeline->insert($docblock, Tag::class);

        $found = [];
        $pipeline->walk(function ($pipe) use (&$found): void {
            $found[] = $pipe::class;
        });
        $this->assertSame([Augmenter\OperationId::class, Augmenter\Docblock::class, Tag::class], $found);
    }

    public function testInsertByMatcher(): void
    {
        $pipeline = new Pipeline([$this->pipe('a'), $this->pipe('c')]);

        $pipeline->insert($this->pipe('b'), fn ($pipes): int => 1);

        $this->assertSame('abc', $pipeline->process(''));
    }

    public function testWalk(): void
    {
        $pipeline = new Pipeline([new Augmenter\OperationId(), new Tag()]);

        $classes = [];
        $pipeline->walk(function ($pipe) use (&$classes): void {
            $classes[] = $pipe::class;
        });

        $this->assertSame([Augmenter\OperationId::class, Tag::class], $classes);
    }

    // --- Grouping ---

    public function testGroupOrderOverridesInsertionOrder(): void
    {
        $log = [];

        $resolve = new class ($log) implements PipeInterface {
            public function __construct(protected array &$log)
            {
            }

            public function group(): string
            {
                return 'resolve';
            }

            public function __invoke(mixed $payload): mixed
            {
                $this->log[] = 'resolve';

                return null;
            }
        };

        $augment = new class ($log) implements PipeInterface {
            public function __construct(protected array &$log)
            {
            }

            public function group(): string
            {
                return 'augment';
            }

            public function __invoke(mixed $payload): mixed
            {
                $this->log[] = 'augment';

                return null;
            }
        };

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
        $pipeline = new Pipeline(
            [new Augmenter\Ref(), new Augmenter\OperationId()],
            groups: [Augmenter\Group::Resolve, Augmenter\Group::Reduce, Augmenter\Group::Augment],
            defaultGroup: Augmenter\Group::Augment,
        );

        $spec = new Specification();
        $result = $pipeline->process($spec);

        $this->assertSame($spec, $result);
    }

    // --- get() ---

    public function testGetReturnsMatchingPipe(): void
    {
        $pipeline = new Pipeline([new Augmenter\OperationId(), new Tag()]);

        $this->assertInstanceOf(Augmenter\OperationId::class, $pipeline->get(Augmenter\OperationId::class));
        $this->assertInstanceOf(Tag::class, $pipeline->get(Tag::class));
    }

    public function testGetReturnsNullForMissing(): void
    {
        $pipeline = new Pipeline([new Augmenter\OperationId()]);

        $this->assertNotInstanceOf(Tag::class, $pipeline->get(Tag::class));
    }

    // --- No groups (BC) ---

    public function testNoGroupsUsesInsertionOrder(): void
    {
        $pipeline = new Pipeline([$this->pipe('a'), $this->pipe('b'), $this->pipe('c')]);

        $this->assertSame('abc', $pipeline->process(''));
    }
}
