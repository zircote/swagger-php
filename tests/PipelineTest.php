<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Pipeline;

class PipelineTest extends OpenApiTestCase
{
    protected function pipe(string $add)
    {
        return new class($add) {
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

    public function testRemoved()
    {
        $pipeline = new Pipeline();

        $pipeline->add($pipec = $this->pipe('c'));
        $pipeline->add($piped = $this->pipe('d'));
        $this->assertEquals('cd', $pipeline->process(''));

        $pipeline->remove($pipec);
        $this->assertEquals('d', $pipeline->process(''));
    }

    public function testInsert()
    {
        $pipeline = new Pipeline();

        $pipeline->add($this->pipe('x'));
        $pipeline->add($this->pipe('z'));
        $this->assertEquals('xz', $pipeline->process(''));

        $pipeline->insert($this->pipe('y'), function ($pipes) { return 1; });
        $this->assertEquals('xyz', $pipeline->process(''));
    }
}
