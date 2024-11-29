<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Pipeline;

class PipelineTest extends OpenApiTestCase
{
    public function __invoke($payload)
    {
        return $payload . 'x';
    }

    protected function pipe(string $add)
    {
        return new class ($add) {
            protected $add;

            public function __construct(string $add)
            {
                $this->add = $add;
            }

            // ------------------------------------------------------------------------

            public function __invoke($payload)
            {
                return $payload . $this->add;
            }
        };
    }

    public function testProcess()
    {
        $pipeline = new Pipeline([$this->pipe('x')]);
        $result = $pipeline->process('');

        $this->assertEquals('x', $result);
    }

    public function testAdd()
    {
        $pipeline = new Pipeline();

        $pipeline->add($this->pipe('a'));
        $this->assertEquals('a', $pipeline->process(''));

        $pipeline->add($this->pipe('b'));
        $this->assertEquals('ab', $pipeline->process(''));
    }

    public function testRemoveStrict()
    {
        $pipeline = new Pipeline();

        $pipeline->add($pipec = $this->pipe('c'));
        $pipeline->add($this->pipe('d'));
        $this->assertEquals('cd', $pipeline->process(''));

        $pipeline->remove($pipec);
        $this->assertEquals('d', $pipeline->process(''));
    }

    public function testRemoveMatcher()
    {
        $pipeline = new Pipeline();

        $pipeline->add($pipec = $this->pipe('c'));
        $pipeline->add($this->pipe('d'));
        $this->assertEquals('cd', $pipeline->process(''));

        $pipeline->remove(null, function ($pipe) use ($pipec) { return $pipe !== $pipec; });
        $this->assertEquals('d', $pipeline->process(''));
    }

    public function testRemoveClassString()
    {
        $pipeline = new Pipeline();

        $pipeline->add($this->pipe('c'));
        $pipeline->add($this);
        $this->assertEquals('cx', $pipeline->process(''));

        $pipeline->remove(__CLASS__);
        $this->assertEquals('c', $pipeline->process(''));
    }

    public function testInsertMatcher()
    {
        $pipeline = new Pipeline();

        $pipeline->add($this->pipe('x'));
        $pipeline->add($this->pipe('z'));
        $this->assertEquals('xz', $pipeline->process(''));

        $pipeline->insert($this->pipe('y'), function ($pipes) { return 1; });
        $this->assertEquals('xyz', $pipeline->process(''));
    }

    public function testInsertClassString()
    {
        $pipeline = new Pipeline();

        $pipeline->add($this);
        $pipeline->add($this->pipe('y'));
        $this->assertEquals('xy', $pipeline->process(''));

        $pipeline->insert($this->pipe('a'), __CLASS__);
        $this->assertEquals('axy', $pipeline->process(''));
    }
}
