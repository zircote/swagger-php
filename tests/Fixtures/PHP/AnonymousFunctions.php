<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

/**
 * @OA\Info(title="Foobar", version="1.0")
 */
class AnonymousFunctions
{
    public function index($ding)
    {
        array_map(static function ($item) use ($ding) {
            return '';
        }, []);
    }

    protected function query()
    {
        return new class() {
            public function leftJoin(string $foo, callable $callback)
            {
                return $this;
            }
        };
    }

    public function other()
    {
        return $this->query()
            ->leftJoin('foo', function ($join) {
                $join->on('user.foo_id', 'foo.id');
            })
            ->leftJoin('bar', function ($join) {
                $join->on('user.bar_id', 'bar.id');
            })
            ->get();
    }

    public function shortFn(): callable
    {
        return fn () => strlen('3');
    }

    public function staticShortFn(): callable
    {
        return static fn () => strlen('3');
    }

    public function withUse($foo): callable
    {
        return function () use ($foo) {
            return false;
        };
    }

    public function dollarCurly1(string $key = 'xx')
    {
        preg_replace("/:${key}/", 'y', 'abx');

        $this->shortFn();
    }

    public function dollarCurly2(string $key = 'xx')
    {
        preg_replace("/:${key}/", 'y', 'abx');

        array_map(static function ($issue) use ($key) {
            return $issue;
        }, []);
    }

    public function curlyOpen(string $key = 'xx')
    {
        $s = "a {$key}";

        array_map(static function ($issue) use ($key) {
            return $issue;
        }, []);
    }
}
