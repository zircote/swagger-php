<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

use OpenApi\Annotations\Info;

/**
 * @OA\Info(title="Foobar", version="1.0")
 */
class AnonymousFunctions
{
    public function index()
    {
        array_map(function ($item) {
            return '';
        }, []);
    }

    protected function query()
    {
        return new class() {
            public function leftJoin(string $foo, callable $callback) {
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
}
