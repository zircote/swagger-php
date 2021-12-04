<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

use OpenApi\Annotations\Info;

#[Info(
    title: 'Foobar',
    version: '1.0',
)]
class AnonymousFunction
{
    public function index()
    {
        array_map(function ($item) {
            return '';
        }, []);
    }
}
