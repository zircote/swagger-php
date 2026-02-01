<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Pipeline;

final class PipelineTest extends OpenApiTestCase
{
    public function __invoke(string $payload): string
    {
        return $payload . 'x';
    }

    protected function pipe(string $add): object
    {
        return new class ($add) {
            protected string $add;

            public function __construct(string $add)
            {
                $this->add = $add;
            }

            public function __invoke(string $payload): string
            {
                return $payload . $this->add;
            }
        };
    }

    public function testProcess(): void
    {
        $pipeline = new Pipeline([$this->pipe('x')]);
        $result = $pipeline->process('');

        $this->assertEquals('x', $result);
    }

    public function testAdd(): void
    {
        $pipeline = new Pipeline();

        $pipeline->add($this->pipe('a'));
        $this->assertEquals('a', $pipeline->process(''));

        $pipeline->add($this->pipe('b'));
        $this->assertEquals('ab', $pipeline->process(''));
    }

    public function testRemoveStrict(): void
    {
        $pipeline = new Pipeline();

        $pipeline->add($pipec = $this->pipe('c'));
        $pipeline->add($this->pipe('d'));
        $this->assertEquals('cd', $pipeline->process(''));

        $pipeline->remove($pipec);
        $this->assertEquals('d', $pipeline->process(''));
    }

    public function testRemoveMatcher(): void
    {
        $pipeline = new Pipeline();

        $pipeline->add($pipec = $this->pipe('c'));
        $pipeline->add($this->pipe('d'));
        $this->assertEquals('cd', $pipeline->process(''));

        $pipeline->remove(null, fn ($pipe): bool => $pipe !== $pipec);
        $this->assertEquals('d', $pipeline->process(''));
    }

    public function testRemoveClassString(): void
    {
        $pipeline = new Pipeline();

        $pipeline->add($this->pipe('c'));
        $pipeline->add($this);
        $this->assertEquals('cx', $pipeline->process(''));

        $pipeline->remove(self::class);
        $this->assertEquals('c', $pipeline->process(''));
    }

    public function testInsertMatcher(): void
    {
        $pipeline = new Pipeline();

        $pipeline->add($this->pipe('x'));
        $pipeline->add($this->pipe('z'));
        $this->assertEquals('xz', $pipeline->process(''));

        $pipeline->insert($this->pipe('y'), fn ($pipes): int => 1);
        $this->assertEquals('xyz', $pipeline->process(''));
    }

    public function testInsertClassString(): void
    {
        $pipeline = new Pipeline();

        $pipeline->add($this);
        $pipeline->add($this->pipe('y'));
        $this->assertEquals('xy', $pipeline->process(''));

        $pipeline->insert($this->pipe('a'), self::class);
        $this->assertEquals('axy', $pipeline->process(''));
    }
}
